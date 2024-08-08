<script setup>
defineProps({
    open:Boolean
});
</script>

<template>

		<div class="block w-5 ">
			<span
				aria-hidden="true"
				class="block absolute h-0.5 w-5 bg-current transform transition duration-500 ease-in-out"
				:class="{'rotate-45': open,' -translate-y-1.5': !open }"
			></span>
			<span
				aria-hidden="true"
				class="block absolute  h-0.5 w-5 bg-current   transform transition duration-500 ease-in-out"
				:class="{'opacity-0': open } "
			></span>
			<span
				aria-hidden="true"
				class="block absolute  h-0.5 w-5 bg-current transform  transition duration-500 ease-in-out"
				:class="{'-rotate-45': open, ' translate-y-1.5': !open}"
			></span>
		</div>

</template>
