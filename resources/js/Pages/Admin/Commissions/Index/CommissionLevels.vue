<script setup>
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { HiSolidPlus } from "oh-vue-icons/icons";

const props = defineProps({
    type: String,
    title: String,
    commissions: { type: Array, default: () => [] },
});
const form = useForm({
    commissions: props.commissions,
});
const setting = computed({
    get: () => {
        const setstr = `${props.type}_commission`;
        return usePage().props.settings?.[setstr] ?? false;
    },
    set: (val) => {
        const setstr = `${props.type}_commission`;
        useForm({ [setstr]: val }).put(
            window.route("admin.settings.update", setstr)
        );
    },
});

const deleteCommissionForm = useForm({});
const commissionBeingDeleted = ref(null);
const add = () => {
    const next = form.commissions.length + 1;
};
const deleteCommission = () => {
    deleteCommissionForm.delete(
        window.route(
            "admin.commissions.destroy",
            commissionBeingDeleted.value?.id
        ),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => (commissionBeingDeleted.value = null),
        }
    );
};

const removeCommission = (index) => {
    
};
</script>
<template>
    <div class="card border-0 card-border">
        <div class="card-body px-0 card-gutterless h-full">
            <div class="grid sm:grid-cols-2 mb-4 px-6">
                <h3>{{ title }}</h3>
                <Switch v-model="setting"></Switch>
            </div>
            <div>
                <CollapseTransition>
                    <div
                        v-for="commission in form.commissions"
                        :key="commission.id"
                    >
                        <FormInput
                            :label="
                                $t('Level  #:level', {
                                    level: commission.level,
                                })
                            "
                            v-model="commission.percent"
                        >
                            <template #trail>%</template>
                        </FormInput>
                        <PrimaryButton
                            class="uppercase tracking-widest text-xs font-semibold"
                            @click.prevent="commissionBeingDeleted = commission"
                            error
                        >
                            Delete</PrimaryButton
                        >
                    </div>
                </CollapseTransition>
                <div class="flex items-center">
                    <PrimaryButton primary> Save</PrimaryButton>
                    <PrimaryButton success @click="add">
                        <VueIcon
                            :icon="HiSolidPlus"
                            class="mr-1 -ml-2"
                        ></VueIcon>
                        Add</PrimaryButton
                    >
                </div>
            </div>
        </div>
    </div>
</template>
