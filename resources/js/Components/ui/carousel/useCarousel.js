import { onMounted, ref } from "vue";

import { createInjectionState } from "@vueuse/core";
import emblaCarouselVue from "embla-carousel-vue";

const [useProvideCarousel, useInjectCarousel] = createInjectionState(
    ({ opts, orientation, plugins }, emits) => {
        const [emblaNode, emblaApi] = emblaCarouselVue(
            {
                ...opts,
                axis: orientation === "horizontal" ? "x" : "y",
            },
            plugins,
        );

        function scrollPrev() {
            emblaApi.value?.scrollPrev();
        }
        function scrollNext() {
            emblaApi.value?.scrollNext();
        }
        function scrollTo(index) {
            emblaApi.value?.scrollTo(index);
        }

        const canScrollNext = ref(false);
        const canScrollPrev = ref(false);
        const selectedIndex = ref(false);
        const scrollSnaps = ref([]);

        function onSelect(api) {
            canScrollNext.value = api?.canScrollNext() || false;
            canScrollPrev.value = api?.canScrollPrev() || false;
            selectedIndex.value = api?.selectedScrollSnap() || 0;

        }

        onMounted(() => {
            if (!emblaApi.value) return;
            emblaApi.value?.on("init", onSelect);
            emblaApi.value?.on("reInit", onSelect);
            emblaApi.value?.on("select", onSelect);
            scrollSnaps.value = emblaApi.value.scrollSnapList();
            emits("init-api", emblaApi.value);
        });

        return {
            carouselRef: emblaNode,
            carouselApi: emblaApi,
            canScrollPrev,
            canScrollNext,
            scrollPrev,
            scrollNext,
            scrollTo,
            orientation,
            scrollSnaps,
            selectedIndex
        };
    },
);

function useCarousel() {
    const carouselState = useInjectCarousel();

    if (!carouselState)
        throw new Error("useCarousel must be used within a <Carousel />");

    return carouselState;
}

export { useCarousel, useProvideCarousel };
