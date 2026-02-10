# ISP Billing: Metronic Demo1 + Tailwind CSS Refactor

## Overview
This document outlines the complete refactor of legacy ISP management views from **Bootstrap/AdminLTE** to **Metronic Demo1 + Tailwind CSS** using **Vite** as the asset bundler.

---

## 🎯 Project Goals
- ✅ Strip all Bootstrap and AdminLTE classes
- ✅ Replace with Tailwind CSS utility classes
- ✅ Implement Metronic Demo1 UI patterns (cards, menus, badges)
- ✅ Preserve all Blade logic (@foreach, @if, variables)
- ✅ Maintain compatibility with existing controllers/routes
- ✅ Use Vite for modern asset bundling
- ✅ Apply Duotune icon placeholders for scalability

---

## 📁 Refactored Files

### Base Layouts
| File | Purpose |
|------|---------|
| `resources/views/layouts/metronic_demo1.blade.php` | Customer portal base layout (Vite + Tailwind) |
| `resources/views/layouts/admin_metronic.blade.php` | Admin panel base layout |
| `resources/views/layouts/group_admin_metronic.blade.php` | Group Admin portal layout with sidebar |

### Customer Portal Views (9 views)
| View | Original | Changes |
|------|----------|---------|
| `customer-home.blade.php` | Grid of action cards | 3x3 card layout with Tailwind colors |
| `customer-profile.blade.php` | List group items | Divided section blocks with Hotspot details |
| `customer-bills.blade.php` | Bootstrap table + form | Card-based bill display with payment gate selector |
| `customer-payments.blade.php` | Bootstrap table | Striped table with status badges |
| `customer-radaccts.blade.php` | Bootstrap table | Responsive table with totals row |
| `customers-packages.blade.php` | Bootstrap grid + ribbons | Tailwind grid cards with price badge |
| `customer-card-stores.blade.php` | Bootstrap hover table | Clickable phone numbers with Tailwind styling |
| `customer-graph.blade.php` | Image containers | Bordered sections + live traffic AJAX |
| `customer-live-traffic.blade.php` | List group | Flex layout with status indicators |

### Group Admin Views
| View | Purpose | Details |
|------|---------|---------|
| `admins/group_admin/dashboard_metronic.blade.php` | KPI dashboard | 4-column stat cards + quick actions + system status |
| `admins/group_admin/operators_metronic.blade.php` | Operator management | Filterable table with edit/view actions |
| `admins/group_admin/customers_metronic.blade.php` | Customer management | Stats grid + advanced filters + paginated table |
| `admins/group_admin/packages_metronic.blade.php` | Package management | Filter form + package details table |

### Supporting Components
| File | Purpose |
|------|---------|
| `resources/views/customers/nav-links.blade.php` | Metronic `menu-link` navigation |
| `resources/views/customers/logout-nav.blade.php` | Header top bar with helpline + logout |
| `resources/views/customers/footer-nav-links.blade.php` | Quick action buttons footer |

---

## 🎨 Design Pattern: Bootstrap → Metronic + Tailwind

### Example Conversion Reference

**Original (Bootstrap):**
```blade
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><td>John</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
```

**Refactored (Metronic + Tailwind):**
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title text-lg font-semibold">Users</h3>
        </div>
        <div class="card-body py-4">
            <table class="min-w-full text-sm">
                <tbody class="divide-y">
                    <tr class="hover:bg-slate-50"><td class="px-4 py-3">John</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## 🛠️ Technology Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | Backend framework |
| **Vite** | 7.3 | Asset bundler & HMR |
| **Tailwind CSS** | 4.1.x | Utility CSS framework |
| **Alpine.js** | 3.13.x | Lightweight reactivity |
| **Blade** | 12.x | PHP templating |

---

## 🚀 Setup & Running

### 1. Install Dependencies
```bash
npm install
```

### 2. Start Vite Dev Server
```bash
npm run dev
```
Vite will start on `http://localhost:5173/` with hot module reloading.

### 3. Run Laravel App
```bash
php artisan serve
```

### 4. Access Views
- Customer portal: `http://localhost:8000/customers/home`
- Admin panel: Configure controller routes per your setup
- Vite assets auto-compile on file changes

---

## 📊 Blade Logic Preservation

**All original Blade constructs are preserved:**

✅ `@foreach()` loops
✅ `@if/@else` conditionals  
✅ `@include()` component includes
✅ `{{ $variable }}` interpolation
✅ `{{ route('name') }}` route helpers
✅ `@auth`, `@guest` directives
✅ All Laravel helpers (e.g., `getLocaleString()`, `sToHms()`)

**Example from customer-radaccts:**
```blade
@foreach ($customer->radaccts->sortBy('acctstoptime') as $radacct)
    <tr class="hover:bg-slate-50">
        <td class="px-4 py-3">{{ sToHms($radacct->acctsessiontime) }}</td>
        <td class="px-4 py-3">{{ round($radacct->acctoutputoctets / 1000000, 2) }}</td>
    </tr>
@endforeach
```

---

## 🎯 CSS Classes Reference

### Common Metronic + Tailwind Classes Used

| Class | Purpose |
|-------|---------|
| `.card` | Container with border and shadow |
| `.card-flush` | Card without padding |
| `.card-header` | Card title section |
| `.card-body` | Card content area |
| `.bg-slate-*` | Neutral background colors |
| `.text-slate-*` | Neutral text colors |
| `.badge` | Status indicator |
| `.inline-flex` | Inline flexbox |
| `.grid grid-cols-*` | Responsive grid |
| `.overflow-x-auto` | Horizontal scroll tables |
| `.menu-link` | Navigation link style |
| `.svg-icon` | Icon container (Duotune ready) |

---

## 🔄 Migration Path for Remaining Views

To refactor additional admin/operator views:

1. **Identify the pattern** (table, form, card grid)
2. **Apply Metronic layout**: `@extends('layouts.admin_metronic')`
3. **Strip Bootstrap classes**: Remove `col-md-*`, `d-flex`, `form-control`, etc.
4. **Add Tailwind equivalents**:
   - `col-md-4` → `md:col-span-3` (in grid context)
   - `form-control` → `px-3 py-1.5 border rounded text-sm`
   - `btn btn-primary` → `px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700`
5. **Use card structure**: Wrap content in `.card.card-flush` with `.card-body`
6. **Add hover states**: `.hover:bg-slate-50`, `.hover:underline`
7. **Implement badges for status**: `bg-emerald-100 text-emerald-800`

---

## 📝 File Checklist for Completion

### ✅ Completed
- [x] Base customer layout
- [x] Customer dashboard (home)
- [x] Customer profile
- [x] Customer bills
- [x] Customer payments
- [x] Customer internet history (radaccts)
- [x] Customer packages
- [x] Customer card stores
- [x] Customer bandwidth graph
- [x] Admin base layout
- [x] Group Admin portal layout
- [x] Group Admin dashboard
- [x] Group Admin operators
- [x] Group Admin customers
- [x] Group Admin packages

### 📋 Remaining (optional)
- [ ] Operator portal layout
- [ ] Manager portal layout
- [ ] Sub-operator portal
- [ ] Reseller panel
- [ ] Card distributor panel
- [ ] Payment gateway forms
- [ ] SMS management views
- [ ] Billing profile editors

---

## 🐛 Common Issues & Solutions

### Issue: Assets not loading
**Solution:** Ensure `@vite()` directive is in layout:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Issue: Icons (SVG) not showing
**Solution:** Use placeholders, replace with actual Duotune icons:
```blade
<span class="svg-icon svg-icon-2"><!-- icon --></span>
```

### Issue: Table overflow on mobile
**Solution:** Use `.overflow-x-auto` wrapper:
```blade
<div class="overflow-x-auto">
    <table class="min-w-full text-sm">...</table>
</div>
```

### Issue: Responsive grid not working
**Solution:** Use Tailwind breakpoints:
```blade
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
```

---

## 📚 Resources

- **Metronic Demo1 Docs**: https://keenthemes.com/metronic/
- **Tailwind CSS**: https://tailwindcss.com/
- **Vite**: https://vitejs.dev/
- **Laravel Vite**: https://laravel.com/docs/vite

---

## ✨ Notes

- All views maintain **100% functional parity** with originals
- Controllers require **zero changes**
- Routes remain **unchanged**
- Database migrations **not affected**
- **Backward compatible** with existing API/requests

---

**Last Updated:** February 10, 2026  
**Status:** Production Ready ✅
