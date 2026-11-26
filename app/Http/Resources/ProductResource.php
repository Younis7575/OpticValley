<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $detail = $request->route('product') !== null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'url' => route('product.show', $this->resource),
            'brand' => $this->whenLoaded('brand', fn () => $this->brand ? ['name' => $this->brand->name, 'slug' => $this->brand->slug] : null),
            'category' => $this->whenLoaded('category', fn () => $this->category ? ['name' => $this->category->name, 'slug' => $this->category->slug] : null),
            'price' => (float) $this->price,
            'sale_price' => $this->hasDiscount() ? (float) $this->sale_price : null,
            'final_price' => $this->final_price,
            'currency' => setting('currency_code', 'PKR'),
            'in_stock' => $this->in_stock,
            'gender' => $this->gender,
            'frame' => ['shape' => $this->frame_shape, 'type' => $this->frame_type, 'material' => $this->frame_material, 'color' => $this->frame_color, 'size' => $this->frame_size],
            'prescription_enabled' => $this->prescription_enabled,
            'rating' => ['average' => $this->rating_avg, 'count' => $this->rating_count],
            'image' => $this->image_url,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($i) => ['url' => $i->url, 'alt' => $i->alt])),
            'short_description' => $this->short_description,
            'description' => $this->when($detail, $this->description),
            'specifications' => $this->when($detail, $this->specifications),
            'variants' => $this->when($detail, fn () => $this->variants->map(fn ($v) => ['id' => $v->id, 'name' => $v->name, 'color' => $v->color_hex, 'in_stock' => $v->stock > 0])),
        ];
    }
}
