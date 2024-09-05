<script setup>
	import { Link } from "@inertiajs/vue3";

	import {
		Breadcrumb,
		BreadcrumbItem,
		BreadcrumbLink,
		BreadcrumbList,
		BreadcrumbPage,
		BreadcrumbSeparator,
	} from "@/Components/ui/breadcrumb";
	defineProps({
		crumbs: Array,
	});
</script>

<template>
	<Breadcrumb>
		<BreadcrumbList>
			<template v-for="(crumb, i) in crumbs" :key="crumb.name">
				<BreadcrumbSeparator v-if="i > 0" />
				<BreadcrumbItem>
					<BreadcrumbLink v-if="crumb.href" as-child>
						<Link
							class="uppercase text-[10px] font-semibold font-inter"
							href="/">
							{{ crumb.name }}
						</Link>
					</BreadcrumbLink>
					<BreadcrumbPage
						class="uppercase text-[10px] font-semibold font-inter"
						v-else>
						{{ crumb.name }}
					</BreadcrumbPage>
				</BreadcrumbItem>
			</template>
		</BreadcrumbList>
	</Breadcrumb>
</template>
