<script setup>
	import { ref } from "vue";

	import { Head, router as Inertia, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiTrash } from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import NoItems from "@/Components/NoItems.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		users: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteUserForm = useForm({});
	const userBeingDeleted = ref(null);

	const deleteUser = () => {
		deleteUserForm.delete(
			window.route("admin.users.destroy", userBeingDeleted.value),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (userBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			Inertia.get(
				window.route("admin.users.index"),
				{ ...(!search ? {} : { search }) },
				{
					preserveState: true,
					preserveScroll: true,
				},
			);
		},
		{
			maxWait: 700,
		},
	);

	const ban = (user) => {
		user.busy = true;
		Inertia.put(
			window.route("admin.users.ban", user.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => (user.busy = false),
			},
		);
	};
</script>
<template>
	<Head :title="title ?? 'Users'" />
	<AdminLayout>
		<main class="h-full container">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Users") }}</h3>
							<p>
								{{
									$t(
										"Theses are registered users on the site",
									)
								}}
							</p>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-between mb-4 px-6">
								<h3 class="mb-4 lg:mb-0">
									<slot />
								</h3>
								<div class="flex justify-end gap-x-3 w-1/2">
									<SearchInput
										class="max-w-xs"
										v-model="search" />
								</div>
							</div>
							<NoItems
								v-if="users.data.length == 0"
								class="border-t dark:border-gray-600">
								No Users Found
							</NoItems>
							<div v-else>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table">
										<thead>
											<tr role="row">
												<th role="columnheader">
													{{ $t("User") }}
												</th>
												<th role="columnheader">
													{{ $t("Email") }}
												</th>
												<th role="columnheader">
													{{ $t("Balance") }}
												</th>
												<th role="columnheader">
													{{ $t("Banned") }}
												</th>
												<th role="columnheader">
													{{ $t("KYC") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>

										<tbody>
											<tr
												v-for="user in users.data"
												:key="user.id"
												role="row">
												<td role="cell">
													<div
														class="flex flex-row align-middle items-center">
														<img
															class="w-5 h-5 mr-2"
															:src="
																user.profile_photo_url
															" />
														<span>
															{{ user.name }}
														</span>
													</div>
												</td>
												<td role="cell">
													{{ user.email }}
												</td>

												<td role="cell">
													<span>
														{{ user.balance }}
													</span>
												</td>
												<td role="cell">
													<label
														class="inline-flex items-center space-x-2">
														<input
															@change="ban(user)"
															v-model="
																user.isBanned
															"
															class="form-switch h-5 w-10 rounded-full bg-green-500 before:rounded-full before:bg-slate-50 checked:!bg-red-500 checked:before:bg-white dark:bg-green-500 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox" />
														<span
															class="text-red-500"
															v-if="
																user.isBanned
															">
															YES
														</span>
														<span
															class="text-green-500"
															v-else>
															NO
														</span>
													</label>
												</td>
												<td role="cell">
													<label
														class="inline-flex items-center space-x-2">
														<input
															:modelValue="
																user.isKycVerified
															"
															disabled
															class="form-switch h-5 w-10 rounded-full bg-red-500 before:rounded-full before:bg-slate-50 checked:!bg-green-500 checked:before:bg-white dark:bg-red-500 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox" />
														<span
															class="text-green-500"
															v-if="
																user.isKycVerified
															">
															YES
														</span>
														<span
															class="text-red-500"
															v-else>
															NO
														</span>
													</label>
												</td>
												<td role="cell">
													<div
														class="flex justify-end space-x-3 text-lg">
														<a
															href="#"
															@click.prevent="
																userBeingDeleted =
																	user.id
															"
															class="hover:text-red-500 border dark:border-gray-600 inline-flex cursor-pointer items-center justify-center p-4 text-center tracking-wide outline-none transition-all duration-200 focus:outline-none disabled:pointer-events-none disabled:opacity-60 h-6 w-6 rounded-full hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25">
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="users.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="userBeingDeleted"
			@close="userBeingDeleted = null">
			<template #title>
				{{ $t("Delete User") }}
			</template>

			<template #content>
				<p>
					{{ $t("Are you sure you would like to delete this User?") }}
				</p>
				<p>{{ $t("The reseller will most likely lose money") }}</p>
			</template>

			<template #footer>
				<PrimaryButton
					secondary
					class="uppercase text-xs font-semibold"
					@click="userBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteUser"
					:class="{ 'opacity-25': deleteUserForm.processing }"
					:disabled="deleteUserForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
