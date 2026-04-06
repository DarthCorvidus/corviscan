# corviscan

Script around `sane`, `convert` and `pngcrush` which takes a directory name:

* `corviscan.php 2026-02-12_acme-power_utility-bill`

It will then scan as TIFF, convert to PNG and use pngcrush to get the filesize down:

```
2026-02-12_acme-power_utility-bill/2026-02-12_acme-power_utility-bill-01.png
2026-02-12_acme-power_utility-bill/2026-02-12_acme-power_utility-bill-02.png
2026-02-12_acme-power_utility-bill/2026-02-12_acme-power_utility-bill-03.png
2026-02-12_acme-power_utility-bill/2026-02-12_acme-power_utility-bill-04.png
```

Currently, settings are hardcoded to 150 dpi, DIN A4 and grayscale.

## Prerequisites

* `sane`
* `imagemagick`
* `pngcrush`