<template>
	<a
		type="button"
		:href="route('connections.connect', 'facebook')"
		class="text-white bg-[#3b5998] hover:bg-[#3b5998]/90 focus:ring-4 focus:outline-none focus:ring-[#3b5998]/50 font-medium rounded-sm text-sm px-5 py-3 text-center inline-flex items-center dark:focus:ring-[#3b5998]/55 transition-colors">
		<svg
			class="w-4 h-4"
			aria-hidden="true"
			xmlns="http://www.w3.org/2000/svg"
			fill="currentColor"
			viewBox="0 0 8 19">
			<path
				fill-rule="evenodd"
				d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z"
				clip-rule="evenodd" />
		</svg>
	</a>
</template>
