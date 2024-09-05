<script setup>
	import { MdSettingssuggestOutlined } from "oh-vue-icons/icons";

	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import CommissionForm from "@/Pages/Admin/Settings/Commission/CommissionForm.vue";

	defineProps({
		levelOne: Object,
		levelTwo: Object,
		levelThree: Object,
	});
</script>
<template>
	<Head title="Commission settings" />
	<AdminLayout>
		<div class="container py-8">
			<div class="grid sm:grid-cols-2">
				<div>
					<div class="flex items-center space-x-1">
						<VueIcon
							:icon="MdSettingssuggestOutlined"
							class="w-6 h-6 text-emerald-500" />
						<h1
							class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
							Commission Settings
						</h1>
					</div>
					<p class="text-gray-600 dark:text-gray-300 mb-4">
						Configure Levels, fees, limmits and settings.
					</p>
				</div>
				<div class="flex justify-end items-center"></div>
			</div>
			<div class="grid sm:grid-cols-2 gap-5 mt-8">
				<CommissionForm level="level_one" :settings="levelOne" />
				<CommissionForm level="level_two" :settings="levelTwo" />
				<CommissionForm level="level_three" :settings="levelThree" />
			</div>
		</div>
	</AdminLayout>
</template>
