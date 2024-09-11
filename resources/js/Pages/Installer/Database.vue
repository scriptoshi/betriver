<script setup>
	import { Link, useForm } from "@inertiajs/vue3";

	import CollapseTransition from "../CollapseTransition.vue";
	const form = useForm({
		connection: "mysql",
		host: "localhost",
		port: "3306",
		database: "",
		username: "",
		password: "",
	});

	const submit = () => {
		form.post(window.route("installer.database.save"));
	};
</script>
<template>
	<div
		class="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
		<div class="w-full mb-8 -mt-8 flex items-center justify-center">
			<svg
				class="fill-gray-300 w-12 h-12"
				id="Layer_1"
				data-name="Layer 1"
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 400.04 392.79">
				<path
					d="M292.2,125.56c-4.6-65.7-57-120.3-124.9-125.2-73.9-5.4-138,51-144.6,124.8l-7.4,83.7L0,381.36l74.8,3.3L79.3,312l2.4-27.4L88.1,212l7.3-82.5A62.31,62.31,0,0,1,163,73c.6.1,1.2.1,1.8.2,26.4,3.1,46.7,22.8,52.9,47.4a66.13,66.13,0,0,1,1.7,21.3l-4.6,51.5,51.6,2.3a65,65,0,0,1,20.9,4.4c23.6,9.3,40.4,32.1,40,58.6a62.32,62.32,0,0,1-63.2,61.4,10.87,10.87,0,0,1-1.8-.1l-82.7-3.6L182,289l6.4-72.6,2.1-24.2,4.4-49.8a36.38,36.38,0,1,0-72.4-7.2c0,.3-.1.5-.1.8l-4.2,46.8-.6,6.2-2.1,24.2-6.4,72.6-2.4,27.4-5.1,72.6,71.5,3.2,83.9,3.7c74,3.2,138.4-52.8,142.8-126.8A135.25,135.25,0,0,0,292.2,125.56Z" />
			</svg>
		</div>
		<div
			class="max-w-md w-full space-y-8 p-8 bg-white shadow-lg rounded-lg">
			<h2 class="text-center text-3xl font-extrabold text-gray-900">
				Database Configuration
			</h2>
			<CollapseTransition>
				<ul
					v-show="form.hasErrors"
					class="mt-1 list-disc ml-3 text-xs font-bold font-inter">
					<li
						v-for="(error, i) in form.errors"
						:key="i"
						class="text-red-500 dark:text-red-400">
						{{ error }}
					</li>
				</ul>
			</CollapseTransition>
			<form @submit.prevent="submit" class="mt-8 space-y-6">
				<div class="rounded-md shadow-sm -space-y-px">
					<div>
						<label for="db_connection" class="sr-only">
							Database Connection
						</label>
						<select
							id="db_connection"
							v-model="form.connection"
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							required>
							<option value="mysql">MySQL</option>
							<option value="pgsql">PostgreSQL</option>
							<option value="sqlite">SQLite</option>
						</select>
					</div>
					<div>
						<label for="db_host" class="sr-only">
							Database Host
						</label>
						<input
							id="db_host"
							v-model="form.host"
							type="text"
							required
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							placeholder="Database Host" />
					</div>
					<div>
						<label for="db_port" class="sr-only">
							Database Port
						</label>
						<input
							id="db_port"
							v-model="form.port"
							type="text"
							required
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							placeholder="Database Port" />
					</div>
					<div>
						<label for="db_name" class="sr-only">
							Database Name
						</label>
						<input
							id="db_name"
							v-model="form.database"
							type="text"
							required
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							placeholder="Database Name" />
					</div>
					<div>
						<label for="db_username" class="sr-only">
							Database Username
						</label>
						<input
							id="db_username"
							v-model="form.username"
							type="text"
							required
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							placeholder="Database Username" />
					</div>
					<div>
						<label for="db_password" class="sr-only">
							Database Password
						</label>
						<input
							id="db_password"
							v-model="form.password"
							type="password"
							class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm"
							placeholder="Database Password" />
					</div>
				</div>

				<div class="flex justify-between">
					<Link
						:href="route('installer.permissions')"
						class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-emerald-600 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
						Back
					</Link>
					<button
						type="submit"
						class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
						Configure Database
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
