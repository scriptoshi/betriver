<template>
	<div>
		<canvas ref="qrCanvas"></canvas>
	</div>
</template>

<script setup>
	import { onMounted, ref, watch } from "vue";

	import QRCode from "qrcode";

	const props = defineProps({
		text: {
			type: String,
			required: true,
		},
		size: {
			type: Number,
			default: 200,
		},
	});

	const qrCanvas = ref(null);

	const generateQR = () => {
		QRCode.toCanvas(
			qrCanvas.value,
			props.text,
			{ width: props.size },
			(error) => {
				if (error) console.error("Error generating QR code:", error);
			},
		);
	};

	onMounted(generateQR);
	watch(() => props.text, generateQR);
</script>
