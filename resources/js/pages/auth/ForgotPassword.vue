<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestAuthLayout from '@/layouts/GuestAuthLayout.vue';
import UiButton from '@/components/ui/UiButton.vue';

defineProps({
    status: {
        type: String,
    },
});

const emailInput = useTemplateRef('email-input');

const forgotPasswordForm = useForm({
    email: '',
});

const submit = () => {
    forgotPasswordForm.post(route('password.email'));
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <GuestAuthLayout>
        <InertiaHead title="Esqueceu sua senha" />

        <template
            v-if="status"
            #message
        >
            <Message
                severity="success"
                :closable="false"
                class="shadow-sm"
            >
                {{ status }}
            </Message>
        </template>

        <template #title>
            <div class="text-center">
                Esqueceu sua senha?
            </div>
        </template>

        <template #subtitle>
            <div class="text-center">
                Preencha seu email para receber um link para atualizar sua senha
            </div>
        </template>

        <form
            class="space-y-6 sm:space-y-8"
            @submit.prevent="submit"
        >
            <div class="flex flex-col gap-2">
                <FloatLabel variant="in">
                    <InputText
                    id="email"
                    ref="email-input"
                    v-model="forgotPasswordForm.email"
                    :invalid="Boolean(forgotPasswordForm.errors?.email)"
                    type="email"
                    autocomplete="username"
                    required
                    fluid
                    />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message
                    v-if="forgotPasswordForm.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ forgotPasswordForm.errors?.email }}
                </Message>
            </div>

            <div>
                <UiButton
                    :loading="forgotPasswordForm.processing"
                    type="submit"
                    label="Enviar email"
                    fluid
                />
            </div>

            <div class="text-center">
                <span class="text-muted-color mr-1">Ou, retorne para</span>
                <InertiaLink :href="route('login')">
                    <UiButton
                        class="p-0"
                        variant="link"
                        label="Login"
                    />
                </InertiaLink>
            </div>
        </form>
    </GuestAuthLayout>
</template>
