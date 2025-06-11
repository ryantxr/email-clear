<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>

    <svg :class="className" v-bind="$attrs" width="100%" height="100%" viewBox="0 0 75 59" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><g><path d="M22.751,6.098l2.999,5.034" style="fill:none;stroke:#000;stroke-width:2.89px;"/><path d="M33.878,3.387l-0,7.197" style="fill:none;stroke:#000;stroke-width:2.89px;"/><path d="M45.098,5.931l-3.572,5.432" style="fill:none;stroke:#000;stroke-width:2.89px;"/></g><g><path d="M59.601,18.713c-0.112,-1.783 -1.651,-3.14 -3.435,-3.027l-49.207,3.102c-1.783,0.113 -3.14,1.652 -3.027,3.435l1.895,30.064c0.113,1.783 1.652,3.139 3.435,3.027l49.208,-3.103c1.783,-0.112 3.139,-1.651 3.027,-3.435l-1.896,-30.063Z" style="fill:#f9c85e;stroke:#e94900;stroke-width:2.89px;"/><path d="M20.535,33.449l-14.926,-13.83" style="fill:none;stroke:#e94900;stroke-width:2.89px;"/><path d="M21.357,39.168l-14.234,14.899" style="fill:none;stroke:#e94900;stroke-width:2.89px;"/><path d="M45.202,32.018l13.283,-15.457" style="fill:none;stroke:#e94900;stroke-width:2.89px;"/></g><g><path d="M24.87,24.248c1.778,-0.223 3.484,1.695 3.807,4.278c0.324,2.584 -0.858,4.863 -2.636,5.085c-1.778,0.223 -3.484,-1.695 -3.807,-4.278c-0.324,-2.584 0.858,-4.863 2.636,-5.085Z"/><ellipse cx="26.215" cy="28.217" rx="0.74" ry="1.032" style="fill:#ebebeb;"/></g><g><ellipse cx="40.131" cy="28.006" rx="3.138" ry="4.291"/><ellipse cx="40.727" cy="27.164" rx="0.698" ry="0.809" style="fill:#dadada;"/></g><path d="M27.715,37.541c1.696,3.898 10.464,5.082 12.367,-1.255" style="fill:none;stroke:#000;stroke-width:3.05px;"/><path d="M52.702,44.609l-4.102,-5.176c-2.542,-2.977 -7.345,-0.027 -4.891,4.207l7.198,9.24c1.719,1.665 3.769,1.357 5.761,-0.895l13.776,-17.588c1.004,-1.508 0.799,-4.044 -0.541,-4.865c-1.289,-0.79 -2.627,-0.764 -4.022,0.319l-13.179,14.758Z" style="fill:#4d9a5b;stroke:#000;stroke-width:2.89px;"/></g></svg>

</template>
