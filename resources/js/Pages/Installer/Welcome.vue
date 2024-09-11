<script setup>
	import { Link } from "@inertiajs/vue3";
</script>
<template>
	<div
		class="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
		<div class="w-full mb-8 -mt-8 flex items-center justify-center">
			<svg
				class="fill-gray-300 w-12 h-12"
				id="Layer_1"
				data-name="Layer 1"
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 400.04 392.79">
				<path
					d="M292.2,125.56c-4.6-65.7-57-120.3-124.9-125.2-73.9-5.4-138,51-144.6,124.8l-7.4,83.7L0,381.36l74.8,3.3L79.3,312l2.4-27.4L88.1,212l7.3-82.5A62.31,62.31,0,0,1,163,73c.6.1,1.2.1,1.8.2,26.4,3.1,46.7,22.8,52.9,47.4a66.13,66.13,0,0,1,1.7,21.3l-4.6,51.5,51.6,2.3a65,65,0,0,1,20.9,4.4c23.6,9.3,40.4,32.1,40,58.6a62.32,62.32,0,0,1-63.2,61.4,10.87,10.87,0,0,1-1.8-.1l-82.7-3.6L182,289l6.4-72.6,2.1-24.2,4.4-49.8a36.38,36.38,0,1,0-72.4-7.2c0,.3-.1.5-.1.8l-4.2,46.8-.6,6.2-2.1,24.2-6.4,72.6-2.4,27.4-5.1,72.6,71.5,3.2,83.9,3.7c74,3.2,138.4-52.8,142.8-126.8A135.25,135.25,0,0,0,292.2,125.56Z" />
			</svg>
		</div>
		<div
			class="max-w-md w-full space-y-8 p-8 bg-white shadow-lg rounded-lg">
			<div>
				<h2
					class="mt-6 text-center text-3xl font-extrabold text-gray-900">
					Welcome to BetRiver
				</h2>
				<p class="mt-2 text-center text-sm text-gray-600">
					Let's set up your application
				</p>
			</div>
			<div class="mt-8 space-y-6">
				<Link
					:href="route('installer.requirements')"
					class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm uppercase font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
					Start Installation
				</Link>
			</div>
		</div>
	</div>
</template>
