<script>
	import draggable from "vuedraggable";

	export default {
		components: {
			draggable,
		},
		props: {
			markets: Array,
		},
		data() {
			return {
				items: [...this.props.markets],
			};
		},
	};
</script>
<template>
	<tbody is="vue:draggable" v-model="marketList" tag="tbody" item-key="id">
		<template #item="{ element: market }">
			<tr role="row">
				<td
					class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
					{{ market.name }}
				</td>
				<td
					class="px-6 py-4 uppercase whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
					{{ market.sport }}
				</td>
				<td
					class="px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
					<Switch
						:modelValue="market.active"
						@update:modelValue="toggle(market)">
						<template v-if="market.active">
							{{ "Exchange Active" }}
						</template>
						<template v-else>
							{{ "Exchange Disabled" }}
						</template>
					</Switch>
				</td>
				<td
					class="rounded-r-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
					<Switch
						:modelValue="market.active"
						@update:modelValue="toggle(market)">
						<template v-if="market.active">
							{{ "Bookie Active" }}
						</template>
						<template v-else>
							{{ "Bookie Disabled" }}
						</template>
					</Switch>
				</td>
			</tr>
		</template>
	</tbody>
</template>
