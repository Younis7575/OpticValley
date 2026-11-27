import AppKit
// usage: urdu "text" fontName size hexColor out.png
let a = CommandLine.arguments
let text = a[1], fontName = a[2], size = CGFloat(Double(a[3])!), hex = a[4], out = a[5]
func col(_ h: String) -> NSColor { var v: UInt64 = 0; Scanner(string: h).scanHexInt64(&v)
  return NSColor(srgbRed: CGFloat((v >> 16) & 255)/255, green: CGFloat((v >> 8) & 255)/255, blue: CGFloat(v & 255)/255, alpha: 1) }
let font = NSFont(name: fontName, size: size) ?? NSFont.systemFont(ofSize: size)
let para = NSMutableParagraphStyle(); para.alignment = .center; para.baseWritingDirection = .rightToLeft
let attrs: [NSAttributedString.Key: Any] = [.font: font, .foregroundColor: col(hex), .paragraphStyle: para]
let s = NSAttributedString(string: text, attributes: attrs)
let b = s.boundingRect(with: NSSize(width: 4000, height: 2000), options: [.usesLineFragmentOrigin, .usesFontLeading])
let pad: CGFloat = size * 0.6
let w = Int(ceil(b.width + pad * 2)), h = Int(ceil(b.height + pad * 2))
let rep = NSBitmapImageRep(bitmapDataPlanes: nil, pixelsWide: w, pixelsHigh: h, bitsPerSample: 8, samplesPerPixel: 4, hasAlpha: true, isPlanar: false, colorSpaceName: .deviceRGB, bytesPerRow: 0, bitsPerPixel: 0)!
NSGraphicsContext.saveGraphicsState(); NSGraphicsContext.current = NSGraphicsContext(bitmapImageRep: rep)
s.draw(with: NSRect(x: pad, y: pad, width: b.width, height: b.height), options: [.usesLineFragmentOrigin, .usesFontLeading])
NSGraphicsContext.restoreGraphicsState()
try! rep.representation(using: .png, properties: [:])!.write(to: URL(fileURLWithPath: out))
