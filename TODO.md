# AAG Website Fix — Task Checklist

## services.html
- [x] Fix broken VIP Transportation card (added missing `<a>` wrapper, service-overlay, service-card-content)
- [x] Add Concierge / VIP Security as full service card → concierge-vip-security.html
- [x] Add Inflight Catering as full service card → inflight-catering.html
- [x] Result: 6 cards total in services-grid (3×2 desktop, 2×3 tablet, 1 col mobile)

## assets/css/styles.css
- [x] Remove duplicate `services-grid` definition
- [x] Remove duplicate `service-card` definition
- [x] Fix `grid-3`: 3 cols desktop → 2 cols ≤900px → 1 col ≤600px
- [x] Add comprehensive mobile improvements (padding, font sizes, card heights, footer, buttons)
- [x] services-grid: 3 cols desktop → 2 cols ≤1024px → 1 col ≤600px

## Verification
- [ ] Open services.html in browser and verify 6 cards display correctly
- [ ] Test mobile view at 375px, 480px, 768px
- [ ] Confirm "Also Available" section is untouched
