<script setup>
import {ref} from "vue";

import {useClipboard, useElementHover} from "@vueuse/core";
defineProps({
	text: {
		type: [String, Number],
		required: true,
	},
	after: Boolean,
});
const {copy, copied} = useClipboard({
	copiedDuring: 1000,
});
const hover = ref(null);
const isHovered = useElementHover(hover);
</script>

<template>
	<div
		ref="hover"
		@click.prevent="copy(text)"
		class="items-center flex"
		:class="[{[$style.copying]: copied}, $style.clickToCopy, 'inline']"
	>
		<span
			data-label="Copied"
			v-if="!after"
			:class="[{[$style.hidden]: !isHovered}, $style.label, 'ml-1 mr-1']"
			>{{ $t("Copy") }}</span
		>
		<span
			class="font-medium mr-1"
			:class="[$style.clickToCopyText]"
		>
			<slot :copied="copied" />
		</span>
		<span
			data-label="Copied"
			v-if="after"
			:class="[{[$style.hidden]: !isHovered}, $style.label, 'ml-1 mr-1 font-semibold']"
			>{{ $t("Copy") }}</span
		>
	</div>
</template>

<style module>
@keyframes floatup {
	20% {
		opacity: 0.999;
	}

	100% {
		transform: translate3d(-50%, -17px, 0);
	}
}

.clickToCopy {
	cursor: pointer;
}

.clickToCopy .clickToCopyText.Highlighted {
	background-color: #d16c25;
	color: #fff;
}

.clickToCopy .label {
	color: #d16c25;
	display: inline-block;
	font-size: 13px;
	line-height: 100%;
	position: relative;
	opacity: 0.999;
	transition: opacity 0.2s ease-in-out;
	top: -1px;
}

.clickToCopy .label.hidden {
	opacity: 0.001;
}

.clickToCopy .label::after {
	content: attr(data-label);
	color: #d16c25;
	display: inline-block;
	position: absolute;
	top: -2px;
	left: 50%;
	opacity: 0.001;
	text-align: center;
	transform: translate3d(-50%, 0, 0);
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
	white-space: nowrap;
}

.clickToCopy.copying .label::after {
	animation: floatup 0.5s ease-in-out;
}
</style>
