<template>
	<li class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
		<div class="flex flex-col sm:flex-row">
			<!-- Score Block -->
			<div
				class="flex items-center justify-center w-full sm:w-20 bg-gray-700 text-white text-2xl font-bold p-4 sm:p-0">
				<div>0</div>
				<div class="mx-1">:</div>
				<div class="text-green-500">1</div>
			</div>

			<!-- Main Content -->
			<div class="flex-1 p-4">
				<div class="text-xs text-gray-400 mb-2">
					<a href="/sport/football" class="hover:underline">
						Football
					</a>
					<span class="mx-1">•</span>
					<a
						href="/listing/sport/football/finland-kakkonen-group-b"
						class="hover:underline">
						Finnish Kakkonen Group B
					</a>
				</div>

				<a
					href="/event/44018426/sport/football/finland-kakkonen-group-b/2024/08/30/15-30/fc-honka-vs-sexypoxyt"
					class="block mb-4">
					<h2 class="text-lg font-bold text-white">
						<div>FC Honka Akatemia</div>
						<div>SexyPöxyt</div>
					</h2>
				</a>

				<div
					class="flex flex-wrap items-center text-sm text-gray-400 mb-4">
					<div
						class="bg-red-600 text-white px-2 py-1 rounded mr-2 mb-2 sm:mb-0">
						<span class="font-bold">16'</span>
					</div>
					<span class="mr-4 mb-2 sm:mb-0">
						Traded:
						<span class="font-bold text-white">£175</span>
					</span>
					<span>87 markets available</span>
				</div>
			</div>

			<!-- Live Feed Button -->
			<button
				class="hidden sm:flex items-center justify-center w-16 bg-gray-700 text-gray-400 hover:text-white transition-colors duration-200">
				<svg
					class="w-6 h-6"
					fill="none"
					stroke="currentColor"
					viewBox="0 0 24 24"
					xmlns="http://www.w3.org/2000/svg">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
			</button>
		</div>

		<!-- Betting Odds -->
		<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-gray-700 p-4">
			<div
				v-for="(team, index) in ['Honka Akatemia', 'Draw', 'SexyPöxyt']"
				:key="index"
				class="text-white">
				<div class="text-sm mb-2">{{ team }}</div>
				<div class="flex justify-between text-xs">
					<div>
						<div class="bg-green-600 px-2 py-1 rounded">
							{{
								index === 1
									? "6.2"
									: index === 2
									? "7.0"
									: "1.35"
							}}
						</div>
						<div class="mt-1 text-gray-400">
							£{{
								index === 1 ? "35" : index === 2 ? "23" : "195"
							}}
						</div>
					</div>
					<div>
						<div class="bg-blue-600 px-2 py-1 rounded">
							{{
								index === 1
									? "7.4"
									: index === 2
									? "8.4"
									: "1.47"
							}}
						</div>
						<div class="mt-1 text-gray-400">
							£{{
								index === 1 ? "38" : index === 2 ? "35" : "28"
							}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</li>
</template>
