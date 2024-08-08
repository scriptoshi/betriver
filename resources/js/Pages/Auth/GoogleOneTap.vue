<script setup>
import { onMounted, ref } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useDark } from "@vueuse/core";
const isDark = useDark();
const props = defineProps({
    signup: { type: Boolean, default: false },
    connect: { type: Boolean, default: false },
    clientId: String,
});

const handleCredentialResponse = ({ credential }) => {
    useForm({
        credential,
        signup: props.signup,
        connect: props.connect,
    }).post(window.route("connections.onetap", { connector: "google" }), {
        preserveState: false,
    });
};
const googleid = ref();
const init = () => {
    if (!window.google) return false;
    window.google.accounts.id.initialize({
        client_id: props.clientId,
        callback: handleCredentialResponse,
        context: props.signup ? "signup" : "signin",
        ux_mode: "popup",
        itp_support: "true",
    });
    window.google.accounts.id.renderButton(googleid.value, {
        type: "standard",
        shape: "rectangular",
        theme: isDark.value ? "filled_black" : "outline",
        text: props.signup ? "signup_with" : "signin_with",
        size: "large",
        logo_alignment: "left",
    });
    window.google.accounts.id.prompt();
    return true;
};

onMounted(() => {
    if (!init()) {
        const oneTap = document.createElement("script");
        oneTap.setAttribute("src", "https://accounts.google.com/gsi/client");
        document.head.appendChild(oneTap);
        oneTap.addEventListener("load", init);
    }
});
</script>
<template>
    <div
        :style="{ colorScheme: isDark ? 'dark' : 'light' }"
        ref="googleid"
    ></div>
</template>
<style>
iframe {
    color-scheme: normal;
}
</style>
