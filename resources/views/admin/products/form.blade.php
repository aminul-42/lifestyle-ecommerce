{{-- Shared by create.blade.php and edit.blade.php. Expects: $categories, optional $product --}}

<div class="form-row">
    <div class="form-group">
        <label>Product Name <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
        <span class="field-error">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <label>SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" placeholder="Optional, auto-generated if blank">
        <span class="field-error">{{ $errors->first('sku') }}</span>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Category <span class="req">*</span></label>
        <select name="category_id" required>
            <option value="">Select category</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <span class="field-error">{{ $errors->first('category_id') }}</span>
    </div>
    <div class="form-group">
        <label>Gender <span class="req">*</span></label>
        <select name="gender" required>
            @foreach (['men','women','kids','unisex'] as $g)
                <option value="{{ $g }}" {{ old('gender', $product->gender ?? 'unisex') == $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
            @endforeach
        </select>
        <span class="field-error">{{ $errors->first('gender') }}</span>
    </div>
</div>

<div class="form-group">
    <label>Short Description</label>
    <input type="text" name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" maxlength="500" placeholder="One-liner shown in listings">
    <span class="field-error">{{ $errors->first('short_description') }}</span>
</div>

<div class="form-group">
    <label>Full Description</label>
    <textarea name="description" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Weight (kg)</label>
        <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $product->weight ?? '') }}" placeholder="For shipping calc later">
    </div>
    <div class="form-group" style="display:flex; gap:2rem; align-items:center; margin-top:1.9rem;">
        <label class="toggle-label">
            <input type="checkbox" name="is_featured" value="1" class="toggle-input"
                {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <span class="toggle-switch"></span>
            <span>Featured</span>
        </label>
        <label class="toggle-label">
            <input type="checkbox" name="has_variants" value="1" class="toggle-input" id="hasVariants"
                {{ old('has_variants', $product->has_variants ?? false) ? 'checked' : '' }}
                onchange="toggleVariantMode()">
            <span class="toggle-switch"></span>
            <span>Has size/color variants</span>
        </label>
    </div>
</div>

{{-- ── Simple pricing (shown when has_variants is off) ─────── --}}
<div id="simplePricing" class="form-row">
    <div class="form-group">
        <label>Price <span class="req" id="priceReq">*</span></label>
        <input type="number" step="0.01" min="0" name="price" id="priceInput" value="{{ old('price', $product->price ?? '') }}" placeholder="Leave blank to use lowest variant price">
        <span class="field-error">{{ $errors->first('price') }}</span>
    </div>
    <div class="form-group">
        <label>Discount Price</label>
        <input type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price', $product->discount_price ?? '') }}">
        <span class="field-error">{{ $errors->first('discount_price') }}</span>
    </div>
</div>
<div id="simpleStock" class="form-group">
    <label>Stock</label>
    <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}">
</div>

{{-- ── Variants (shown when has_variants is on) ─────────────── --}}
<div id="variantsSection" style="display:none;">
    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.5rem;">Variants</label>
    <div id="variantRows"></div>
    <button type="button" class="btn btn-secondary btn-sm" onclick="addVariantRow()">+ Add Variant</button>
    <span class="field-error">{{ $errors->first('variants') }}</span>
</div>

{{-- ── Images ─────────────────────────────────────────────── --}}
<div class="form-group">
    <label>Product Images</label>
    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" onchange="handleImagePreview(event)">
    <span class="field-error">{{ $errors->first('images.0') }}</span>
</div>

@isset($product)
    @if ($product->images->isNotEmpty())
        <div class="form-group">
            <label>Existing Images</label>
            <div id="existingImages" style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                @foreach ($product->images as $img)
                    <div style="position:relative;" id="existing-image-{{ $img->id }}">
                        <img src="{{ Storage::disk('public')->url($img->path) }}"
                             style="width:70px; height:70px; object-fit:cover; border-radius:9px; border:2px solid {{ $img->is_primary ? 'var(--blue)' : 'var(--border)' }};">
                        @if ($img->is_primary)
                            <span style="position:absolute; top:-6px; left:-6px; background:var(--blue); color:#fff; font-size:0.6rem; padding:1px 5px; border-radius:8px;">Primary</span>
                        @endif
                        <button type="button" onclick="deleteExistingImage({{ $img->id }})"
                            style="position:absolute; top:-8px; right:-8px; width:20px; height:20px; border-radius:50%; background:var(--danger); color:#fff; border:none; cursor:pointer; font-size:0.7rem; line-height:1;">×</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endisset

<div id="imagePreview" style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.5rem;"></div>

<script>
    // ── Variant toggle ─────────────────────────────────────────
    function toggleVariantMode() {
        const on = document.getElementById('hasVariants').checked;
        document.getElementById('variantsSection').style.display = on ? 'block' : 'none';
        document.getElementById('simplePricing').style.display = on ? 'none' : 'grid';
        document.getElementById('simpleStock').style.display = on ? 'none' : 'block';
        if (on && document.getElementById('variantRows').children.length === 0) {
            addVariantRow();
        }
    }

    let variantIndex = 0;
    function addVariantRow(data = {}) {
        const i = variantIndex++;
        const wrap = document.createElement('div');
        wrap.className = 'form-row';
        wrap.style.cssText = 'grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto; align-items:end; margin-bottom:0.75rem;';
        wrap.innerHTML = `
            <div class="form-group" style="margin-bottom:0;">
                <label>Size</label>
                <input type="text" name="variants[${i}][size]" value="${data.size ?? ''}" placeholder="e.g. M">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Color</label>
                <input type="text" name="variants[${i}][color]" value="${data.color ?? ''}" placeholder="e.g. Black">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="variants[${i}][price]" value="${data.price ?? ''}" placeholder="Overrides base">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Discount</label>
                <input type="number" step="0.01" min="0" name="variants[${i}][discount_price]" value="${data.discount_price ?? ''}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Stock</label>
                <input type="number" min="0" name="variants[${i}][stock]" value="${data.stock ?? ''}">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Remove</button>
        `;
        document.getElementById('variantRows').appendChild(wrap);
    }

    // ── Image preview ────────────────────────────────────────
    function handleImagePreview(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        [...e.target.files].forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = ev => {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;';
                div.innerHTML = `
                    <img src="${ev.target.result}" style="width:70px; height:70px; object-fit:cover; border-radius:9px; border:2px solid ${i === 0 ? 'var(--blue)' : 'var(--border)'};">
                    <label style="display:block; text-align:center; font-size:0.65rem; margin-top:2px;">
                        <input type="radio" name="primary_image_index" value="${i}" ${i === 0 ? 'checked' : ''}> Primary
                    </label>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Delete existing image (edit page only) ───────────────
    async function deleteExistingImage(imageId) {
        if (!confirm('Remove this image?')) return;

        try {
            const res = await fetch(`/admin/product-images/${imageId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error();
            document.getElementById(`existing-image-${imageId}`).remove();
            showToast('Image removed.', 'success');
        } catch {
            showToast('Could not remove image.', 'error');
        }
    }

    // Initialize state on load (handles validation-failure redisplay + edit prefill)
    document.addEventListener('DOMContentLoaded', () => {
        toggleVariantMode();
        @isset($product)
            @if ($product->has_variants)
                @foreach ($product->variants as $v)
                    addVariantRow({ size: @json($v->size), color: @json($v->color), price: {{ $v->price ?? 'null' }}, discount_price: {{ $v->discount_price ?? 'null' }}, stock: {{ $v->stock }} });
                @endforeach
            @endif
        @endisset
        @if (old('variants'))
            @foreach (old('variants') as $v)
                addVariantRow({ size: @json($v['size'] ?? ''), color: @json($v['color'] ?? ''), price: @json($v['price'] ?? ''), discount_price: @json($v['discount_price'] ?? ''), stock: @json($v['stock'] ?? '') });
            @endforeach
        @endif
    });
</script>