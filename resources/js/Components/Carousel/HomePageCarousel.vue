<script setup>
	import Autoplay from "embla-carousel-autoplay";

	import ScrollDots from "@/Components/Carousel/ScrollDots.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import {
		Carousel,
		CarouselContent,
		CarouselItem,
		CarouselNext,
		CarouselPrevious,
	} from "@/Components/ui/carousel";
	defineProps({
		slides: Array,
	});
	const plugin = Autoplay({
		delay: 8000,
		stopOnMouseEnter: true,
		stopOnInteraction: false,
	});
</script>

<template>
	<Carousel
		class="flex-shrink-0 basis-full md:basis-1/2 lg:basis-1/3"
		:plugins="[plugin]"
		@mouseenter="plugin.stop"
		@mouseleave="[plugin.reset(), plugin.play(), console.log('Running')]">
		<CarouselContent>
			<CarouselItem v-for="(slide, index) in slides" :key="index">
				<div
					class="inline-flex align-text-top py-6 px-16 h-[244px] relative w-full flex-col justify-end items-start select-none rounded">
					<a
						:href="slide.url"
						class="absolute top-0 left-0 h-full w-full z-0">
						<img
							:src="slide.image"
							class="w-full h-full object-cover" />
					</a>
					<span
						class="text-3xl font-bold font-inter text-white my-2 max-h-[70px] overflow-hidden pointer-events-none z-[1] max-w-xs relative">
						{{ slide.title }}
					</span>
					<span
						class="text-base font-bold leading-5 text-white max-w-[360px] pointer-events-none z-[1] relative whitespace-normal">
						{{ slide.description }}
					</span>
					<PrimaryButton
						primary
						class="z-[1] tracking-widest mt-4 !px-7 !py-3 uppercase font-extrabold font-inter text-xs"
						url
						:href="slide.url">
						{{ slide.button }}
					</PrimaryButton>
				</div>
			</CarouselItem>
		</CarouselContent>
		<CarouselPrevious />
		<CarouselNext />
		<ScrollDots />
	</Carousel>
</template>
