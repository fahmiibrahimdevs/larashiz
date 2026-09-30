# UI, Table, and Styling Guidelines

## 1. Table Standard Structure
Follow the Posts page (`resources/views/livewire/posts/index.blade.php`) pattern:
- Card wrapper: `.card` > `.card-header` > `.card-body.p-0`
- Top controls: `.dt-control-wrapper` containing `.dt-show-entries` and `.dt-search-wrapper`
- Table: `.table-responsive` (smooth horizontal scroll) > `table.table.table-clean` (clean non-striped rows with hover effect)
- `thead`: All `<th>` MUST use `class="text-nowrap"` and `bg-gray-100` / `bg-gray-50`. Primary text columns must not have fixed widths.
- `tbody`: Each row MUST have `wire:key="item-{{ $item->id }}"`, numbering formula `{{ $items->firstItem() + $index }}`, aligned with `.align-middle`.
- Footer: `.dt-footer-wrapper` with entry counter and `$items->links('livewire.custom-pagination')`.

## 2. Component-First CSS Rule
- **NEVER** write arbitrary bracket utility classes (e.g. `text-[11px]`, `text-[#1e293b]`, `bg-[#f8fafc]`, inline `style="..."`) in Blade templates.
- Always declare semantic component classes in `public/assets/css/components-tailwind.css` using `@apply`, and compile via `npm run build:shizuefi`.

## 3. Global Card Shadows and Spacing
- Global card shadow is balanced medium-soft: `box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.04)` + `border: 1px solid #e9edf2`.
- Mobile spacing: `.card` margin on screens <= 767.98px is `margin-bottom: 14px !important`.

## 4. Testing Isolation
- Automated tests MUST use isolated test database `DB_DATABASE=laravel_test` in `phpunit.xml`.
- Never run tests directly against the development database `laravel`.

## 5. Primary Color & Theme Standard
- The project primary brand color is `#0b52aa`.
- NEVER use old default Stisla indigo/purple `#6777ef`.
- Floating action buttons, active chips, and primary buttons must use `#0b52aa` / `.btn-primary`.

## 6. Template-First Component Rule
- Always check if UI component exists in the built-in template (e.g. badges `badge badge-*`, alerts, modals, buttons).
- Use pure template classes. NEVER add arbitrary modifier classes (like `text-dark` on `badge-warning`).
- NEVER create custom component classes for elements already provided by template.
- If an element does not exist in the template and requires custom CSS, ask for user approval first.

## 7. Page Header & Section Title Standard
- Every view under `layouts.app` MUST define `<x-slot name="header">` with `<h1>` and `.section-header-breadcrumb`.
- Right below the header slot, MUST define `<h2 class="section-title">` and `<p class="section-lead mb-3">`.
- **NEVER** re-wrap views in `<section class="section">` or `<div class="section-body">` as `layouts/app.blade.php` already wraps the slot automatically.


