<script setup>
import {computed} from "vue";

import {Head, usePage} from "@inertiajs/vue3";
const meta = computed(() => usePage().props.meta ?? []);
const site = computed(() => usePage().props.config?.appName);
defineProps({title: String});
</script>
<template>
	<Head>
		<title>{{ meta["title"] ?? title ?? site }}</title>
		<template
			v-for="(content, name) in meta"
			:key="name"
		>
			<meta
				v-if="name != 'title'"
				:name="name"
				:content="content"
			/>
		</template>
	</Head>
</template>
