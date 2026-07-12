@extends('layouts.app')

@section('title', 'Store Settings')
@section('page-title', 'Store Settings')
@section('page-subtitle', 'Configure branding, payment, and store information')

@push('styles')
<style>
    .settings-tabs { display: flex; gap: 0.4rem; border-bottom: 1px solid var(--border); margin-bottom: 1.5rem; flex-wrap: wrap; }
    .settings-tab { padding: 0.7rem 1.1rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); background: none; border: none; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: all 0.15s; }
    .settings-tab:hover { color: var(--blue); }
    .settings-tab.active { color: var(--blue); border-bottom-color: var(--blue); }
    .settings-panel { display: none; }
    .settings-panel.active { display: block; }
    .settings-card { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); padding: 1.75rem; }
    .image-upload-box { display: flex; align-items: center; gap: 1.25rem; }
    .image-preview { width: 84px; height: 84px; border-radius: 12px; border: 1.5px dashed var(--border); background: #fafafa; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .image-preview img { width: 100%; height: 100%; object-fit: contain; }
    .image-preview svg { width: 28px; height: 28px; color: #d1d5db; }
    .color-input-row { display: flex; align-items: center; gap: 0.6rem; }
    .color-input-row input[type="color"] { width: 44px; height: 40px; padding: 2px; border: 1.5px solid var(--border); border-radius: 8px; cursor: pointer; background: #fff; }
    .color-input-row input[type="text"] { flex: 1; }
    .settings-save-bar { display: flex; justify-content: flex-end; margin-top: 1.5rem; }
</style>
@endpush

@section('content')

    <form id="settingsForm" enctype="multipart/form-data">

        <div class="settings-tabs">
            <button type="button" class="settings-tab active" data-tab="branding">Branding</button>
            <button type="button" class="settings-tab" data-tab="general">General</button>
            <button type="button" class="settings-tab" data-tab="social">Social</button>
            <button type="button" class="settings-tab" data-tab="payment">Payment</button>
            <button type="button" class="settings-tab" data-tab="seo">SEO</button>
        </div>

        {{-- ── Branding ───────────────────────────────────── --}}
        <div class="settings-panel active" id="panel-branding">
            <div class="settings-card">
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" name="site_name" value="{{ $settings['branding']->firstWhere('key', 'site_name')?->value }}">
                    <span class="field-error" id="err_site_name"></span>
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="site_tagline" value="{{ $settings['branding']->firstWhere('key', 'site_tagline')?->value }}">
                    <span class="field-error" id="err_site_tagline"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Logo</label>
                        <div class="image-upload-box">
                            <div class="image-preview" id="preview_site_logo">
                                @if ($settings['branding']->firstWhere('key', 'site_logo')?->value)
                                    <img src="{{ setting_image('site_logo') }}" alt="Logo">
                                @else
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <input type="file" name="site_logo" accept="image/*" onchange="previewImage(this, 'preview_site_logo')">
                                <span class="field-error" id="err_site_logo"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Favicon</label>
                        <div class="image-upload-box">
                            <div class="image-preview" id="preview_site_favicon">
                                @if ($settings['branding']->firstWhere('key', 'site_favicon')?->value)
                                    <img src="{{ setting_image('site_favicon') }}" alt="Favicon">
                                @else
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <input type="file" name="site_favicon" accept="image/*" onchange="previewImage(this, 'preview_site_favicon')">
                                <span class="field-error" id="err_site_favicon"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Primary Color</label>
                        <div class="color-input-row">
                            <input type="color" id="primary_color_picker" value="{{ $settings['branding']->firstWhere('key', 'primary_color')?->value ?? '#111111' }}" oninput="document.getElementById('primary_color_text').value = this.value">
                            <input type="text" name="primary_color" id="primary_color_text" value="{{ $settings['branding']->firstWhere('key', 'primary_color')?->value }}" oninput="document.getElementById('primary_color_picker').value = this.value">
                        </div>
                        <span class="field-error" id="err_primary_color"></span>
                    </div>
                    <div class="form-group">
                        <label>Accent Color</label>
                        <div class="color-input-row">
                            <input type="color" id="accent_color_picker" value="{{ $settings['branding']->firstWhere('key', 'accent_color')?->value ?? '#c9a227' }}" oninput="document.getElementById('accent_color_text').value = this.value">
                            <input type="text" name="accent_color" id="accent_color_text" value="{{ $settings['branding']->firstWhere('key', 'accent_color')?->value }}" oninput="document.getElementById('accent_color_picker').value = this.value">
                        </div>
                        <span class="field-error" id="err_accent_color"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── General ────────────────────────────────────── --}}
        <div class="settings-panel" id="panel-general">
            <div class="settings-card">
                <div class="form-row">
                    <div class="form-group">
                        <label>Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['general']->firstWhere('key', 'currency_symbol')?->value }}">
                        <span class="field-error" id="err_currency_symbol"></span>
                    </div>
                    <div class="form-group">
                        <label>Currency Code</label>
                        <input type="text" name="currency_code" value="{{ $settings['general']->firstWhere('key', 'currency_code')?->value }}">
                        <span class="field-error" id="err_currency_code"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ $settings['general']->firstWhere('key', 'contact_phone')?->value }}">
                        <span class="field-error" id="err_contact_phone"></span>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" value="{{ $settings['general']->firstWhere('key', 'contact_email')?->value }}">
                        <span class="field-error" id="err_contact_email"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Store Address</label>
                    <textarea name="contact_address" rows="3">{{ $settings['general']->firstWhere('key', 'contact_address')?->value }}</textarea>
                    <span class="field-error" id="err_contact_address"></span>
                </div>
            </div>
        </div>

        {{-- ── Social ─────────────────────────────────────── --}}
        <div class="settings-panel" id="panel-social">
            <div class="settings-card">
                <div class="form-group">
                    <label>Facebook URL</label>
                    <input type="text" name="facebook_url" value="{{ $settings['social']->firstWhere('key', 'facebook_url')?->value }}" placeholder="https://facebook.com/yourpage">
                    <span class="field-error" id="err_facebook_url"></span>
                </div>
                <div class="form-group">
                    <label>Instagram URL</label>
                    <input type="text" name="instagram_url" value="{{ $settings['social']->firstWhere('key', 'instagram_url')?->value }}" placeholder="https://instagram.com/yourpage">
                    <span class="field-error" id="err_instagram_url"></span>
                </div>
                <div class="form-group">
                    <label>WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="{{ $settings['social']->firstWhere('key', 'whatsapp_number')?->value }}" placeholder="+8801XXXXXXXXX">
                    <span class="field-error" id="err_whatsapp_number"></span>
                </div>
            </div>
        </div>

        {{-- ── Payment ────────────────────────────────────── --}}
        <div class="settings-panel" id="panel-payment">
            <div class="settings-card">
                <div class="form-group">
                    <label>Payment Instructions</label>
                    <textarea name="payment_instructions" rows="3">{{ $settings['payment']->firstWhere('key', 'payment_instructions')?->value }}</textarea>
                    <span class="field-error" id="err_payment_instructions"></span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>bKash Number</label>
                        <input type="text" name="bkash_number" value="{{ $settings['payment']->firstWhere('key', 'bkash_number')?->value }}">
                        <span class="field-error" id="err_bkash_number"></span>
                    </div>
                    <div class="form-group">
                        <label>Nagad Number</label>
                        <input type="text" name="nagad_number" value="{{ $settings['payment']->firstWhere('key', 'nagad_number')?->value }}">
                        <span class="field-error" id="err_nagad_number"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Bank Account Details</label>
                    <textarea name="bank_details" rows="3">{{ $settings['payment']->firstWhere('key', 'bank_details')?->value }}</textarea>
                    <span class="field-error" id="err_bank_details"></span>
                </div>
                <div class="form-group">
                    <label class="toggle-label">
                        <input type="checkbox" name="cod_enabled" class="toggle-input" {{ $settings['payment']->firstWhere('key', 'cod_enabled')?->value == '1' ? 'checked' : '' }}>
                        <span class="toggle-switch"></span>
                        Enable Cash on Delivery
                    </label>
                </div>
            </div>
        </div>

        {{-- ── SEO ────────────────────────────────────────── --}}
        <div class="settings-panel" id="panel-seo">
            <div class="settings-card">
                <div class="form-group">
                    <label>Default Meta Title</label>
                    <input type="text" name="meta_title" value="{{ $settings['seo']->firstWhere('key', 'meta_title')?->value }}">
                    <span class="field-error" id="err_meta_title"></span>
                </div>
                <div class="form-group">
                    <label>Default Meta Description</label>
                    <textarea name="meta_description" rows="3">{{ $settings['seo']->firstWhere('key', 'meta_description')?->value }}</textarea>
                    <span class="field-error" id="err_meta_description"></span>
                </div>
            </div>
        </div>

        <div class="settings-save-bar">
            <button type="submit" class="btn btn-primary" id="saveBtn">
                <span class="btn-text">Save Settings</span>
                <span class="btn-loader" style="display:none;">
                    <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    Saving...
                </span>
            </button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
        });
    });

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        clearErrors();
        Object.keys(errors).forEach(field => {
            const el = document.getElementById('err_' + field);
            if (el) el.textContent = errors[field][0];
        });

        // Jump to the first tab that has an error, so the user sees it
        const firstErrorField = Object.keys(errors)[0];
        const el = document.getElementById('err_' + firstErrorField);
        if (el) {
            const panel = el.closest('.settings-panel');
            if (panel && !panel.classList.contains('active')) {
                const tabName = panel.id.replace('panel-', '');
                document.querySelector(`.settings-tab[data-tab="${tabName}"]`)?.click();
            }
        }
    }

    document.getElementById('settingsForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        // Checkbox fix: unchecked boxes don't submit, but we still want explicit presence handled server-side.
        // (Server already treats absence as "0", so no extra action needed here.)

        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch("{{ route('admin.settings.update') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData,
            });
            const json = await parseJson(res);

            if (res.status === 422) {
                showErrors(json.errors || {});
                return;
            }

            if (!res.ok) {
                showToast(json.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(json.message || 'Settings saved.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    });
</script>
@endpush