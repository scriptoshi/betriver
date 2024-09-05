<script setup>
	import { ref } from "vue";

	import { HiSolidMinus, HiSolidPlus } from "oh-vue-icons/icons";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Pagination from "@/Components/Pagination.vue";
	import { Button } from "@/Components/ui/button";
	import {
		Dialog,
		DialogContent,
		DialogDescription,
		DialogFooter,
		DialogHeader,
		DialogTitle,
	} from "@/Components/ui/dialog";
	import {
		Table,
		TableBody,
		TableCaption,
		TableCell,
		TableHead,
		TableHeader,
		TableRow,
	} from "@/Components/ui/table";
	import VueIcon from "@/Components/VueIcon.vue";
	import NoTransactions from "@/Pages/Account/Statement/NoTransactions.vue";
	import Transactable from "./Transactable.vue";
	const txCurrentlyShowing = ref();
	const open = ref(false);
	const showTx = (tx) => {
		txCurrentlyShowing.value = tx;
		open.value = true;
	};
</script>

<template>
	<div class="mb-12">
		<Table class="text-gray-700 dark:text-gray-350">
			<TableCaption v-if="$page.props.transactions?.data?.length > 0">
				A list of your recent transactions.
			</TableCaption>
			<TableHeader>
				<TableRow class="border-gray-250 dark:border-gray-700">
					<TableHead>{{ $t("TXID") }}</TableHead>
					<TableHead>{{ $t("Date") }}</TableHead>
					<TableHead>{{ $t("Description") }}</TableHead>
					<TableHead class="text-right">{{ $t("Amount") }}</TableHead>
				</TableRow>
			</TableHeader>
			<TableBody v-if="$page.props.transactions?.data?.length > 0">
				<TableRow
					class="border-gray-250 dark:border-gray-700"
					v-for="transaction in $page.props.transactions.data"
					:key="transaction.id">
					<TableCell class="font-semibold uppercase">
						<a
							class="text-sky-500 dark:text-sky-400 hover:text-sky-600 dark:hover:text-sky-300 hover:underline"
							@click.prevent="showTx(transaction)"
							href="#">
							{{ transaction.uid }}
						</a>
					</TableCell>
					<TableCell>
						{{ transaction.created_at }}
					</TableCell>

					<TableCell>{{ transaction.description }}</TableCell>
					<TableCell class="text-right">
						<div class="flex items-center justify-end">
							<VueIcon
								class="text-green-600 dark:text-green-400 mr-1 w-4 h-4"
								v-if="transaction.action === 'credit'"
								:icon="HiSolidPlus" />
							<VueIcon
								class="text-red-600 dark:text-red-400 mr-1 w-4 h-4"
								v-else
								:icon="HiSolidMinus" />
							<MoneyFormat
								class="font-semibold text-gray-850 dark:text-white"
								:amount="transaction.amount" />
						</div>
					</TableCell>
				</TableRow>
			</TableBody>
			<TableBody v-else>
				<TableRow
					class="border-gray-250 dark:border-gray-700 pointer-events-none">
					<TableCell colspan="4">
						<div class="w-full text-center min-h-40">
							<div class="w-full mt-4 mb-6 flex justify-center">
								<NoTransactions
									class="text-gray-900 dark:text-white w-32 h-32" />
							</div>
							<p
								class="text-sm font-bold font-inter text-gray-900 dark:text-gray-50">
								Your statement is empty
							</p>
							Try editing the filters or selecting a new time
							period
						</div>
					</TableCell>
				</TableRow>
			</TableBody>
		</Table>
		<Pagination :meta="$page.props.transactions.meta" />
		<Dialog v-model:open="open">
			<DialogContent
				class="sm:max-w-[525px] bg-white dark:bg-gray-850 text-gray-700 dark:text-white">
				<DialogHeader>
					<DialogTitle class="text-gray-900 dark:text-white">
						{{ txCurrentlyShowing.uuid }}
					</DialogTitle>
					<DialogDescription>
						{{ txCurrentlyShowing.description }}
					</DialogDescription>
				</DialogHeader>
				<Transactable
					v-if="txCurrentlyShowing"
					:transaction="txCurrentlyShowing" />
				<DialogFooter>
					<Button
						type="button"
						variant="secondary"
						size="xs"
						@click.prevent="open = false">
						{{ $t("Close") }}
					</Button>
				</DialogFooter>
			</DialogContent>
		</Dialog>
	</div>
</template>
