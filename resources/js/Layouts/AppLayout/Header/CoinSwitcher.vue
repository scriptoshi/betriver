<script setup>
import {computed, onMounted, watch} from "vue";

import {router, usePage} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";

const {chain} = useAccount();
const current = computed(() => {
	const list = usePage().props.chains;
	return list.find((item) => parseInt(item.chainId) === chain?.value?.id);
});
const AuthCheck = computed(() => usePage().props.AuthCheck);
const verified = computed(() => usePage().props.verified);
const coinId = computed({
	set: (value) => {
		if (!AuthCheck.value || !verified.value) return;
		router.post(window.route("user.chain.set"), {
			coin: value,
			chain: chain.id,
		});
	},
	get: () => {
		return usePage().props.coinId;
	},
});
const chainId = computed(() => usePage().props.chainId);
const setChainId = ({chain}) => {
	if (!AuthCheck.value || !verified.value) return;
	router.post(window.route("user.chain.set"), {
		chain,
		coin: current.value.coins[0].id,
	});
};
watch(
	[() => chain?.value, () => chainId.value, AuthCheck],
	([chain, chainId]) => {
		if (!chain?.id) return;
		if (chain.id !== chainId) {
			setChainId({chain: chain.id});
		}
	},
	{
		immediate: true,
	},
);
onMounted(() => {
	if (!coinId.value && current.value?.coins?.length) {
		coinId.value = current.value.coins[0].id;
	}
});
</script>
<template>
	<div
		v-if="current?.coins?.length"
		class="flex items-center space-x-3"
	>
		<a
			href="#"
			v-for="item in current.coins"
			:key="item.id"
			@click.prevent="coinId = item.id"
		>
			<img
				:src="item.logo_uri"
				:alt="item.name"
				v-tippy="{content: item.name}"
				class="w-6 h-6 rounded-full hover:grayscale-0 hover:border border-gray-200 dark:border-gray-800"
				:class="{grayscale: coinId !== item.id}"
			/>
		</a>
	</div>
</template>
