<picture>
  <source srcset="{{ $src }}" type="image/webp">

  <img
    alt="{{ $alt }}"
    class="align-middle"
    decoding="async"
    height="{{ $height ?? 35 }}"
    importance="low"
    loading="lazy"
    referrerpolicy="no-referrer"
    src="{{ sprintf('%s?type=jpg', $src) }}"
    style="{{ $style ?? null }}"
    width="{{ $width ?? 35 }}"
  >

</picture>
