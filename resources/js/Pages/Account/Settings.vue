<script setup>
	import DeleteUserForm from "@/Pages/Account/Settings/DeleteUserForm.vue";
	import ProfilePhoto from "@/Pages/Account/Settings/ProfilePhoto.vue";
	import SettingsLayout from "@/Pages/Account/Settings/SettingsLayout.vue";
	import TwoFactorAuthenticationForm from "@/Pages/Account/Settings/TwoFactorAuthenticationForm.vue";
	import UpdatePasswordForm from "@/Pages/Account/Settings/UpdatePasswordForm.vue";
	import UpdateProfileInformationForm from "@/Pages/Account/Settings/UpdateProfileInformationForm.vue";

	defineProps({
		mustVerifyEmail: {
			type: Boolean,
		},
		status: {
			type: String,
		},
	});
</script>

<template>
	<SettingsLayout>
		<div class="py-2 grid gap-4">
			<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
				<TwoFactorAuthenticationForm
					:requiresConfirmation="
						$page.props.twoFactorRequiresConfirmation ?? false
					" />
			</div>
			<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
				<UpdatePasswordForm class="max-w-xl" />
			</div>
			<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
				<UpdateProfileInformationForm
					:must-verify-email="mustVerifyEmail"
					:status="status"
					class="max-w-xl" />
			</div>

			<div class="grid gap-4">
				<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
					<ProfilePhoto
						:photo="$page.props.auth.user.profile_photo_url"
						class="max-w-xl" />
				</div>
				<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
					<DeleteUserForm class="max-w-xl" />
				</div>
			</div>
		</div>
	</SettingsLayout>
</template>
