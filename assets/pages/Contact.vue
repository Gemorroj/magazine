<template>
    <main>
        <p>Contact page</p>

        <div style="display: flex; align-items: center; height: 100%; width: 100%;">
            <yandex-map :coords="[53.903017, 27.560780]" zoom="16" style="width: 100%; height: 600px;">
                <ymap-marker marker-type="placemark" marker-id="1"
                     :coords="[53.903017, 27.560780]"
                     hint-content="Магазин Umberto Imperial"
                     :balloon="{header: 'Umberto Imperial', body: 'Магазин Umberto Imperial, лучшие шмотки в минске', footer: 'тел.: +375 29 123 12 12'}"
                     :icon="{color: 'green', glyph: 'shopping'}"></ymap-marker>
            </yandex-map>
        </div>
    </main>
</template>

<script>
    import {yandexMap, ymapMarker} from 'vue-yandex-maps';

    export default {
        components: {
            yandexMap,
            ymapMarker
        }
    }
</script>
