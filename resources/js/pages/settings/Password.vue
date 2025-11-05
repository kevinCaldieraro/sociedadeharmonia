<script setup>
import { useTemplateRef } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Password from 'primevue/password';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/UserSettingsLayout.vue';
import UiButton from '@/components/ui/UiButton.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: 'Senha' },
];

const currentPasswordInput = useTemplateRef('current-password-input');
const newPasswordInput = useTemplateRef('new-password-input');

const toast = useToast();
const updatePasswordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showSuccessToast = () => {
    toast.add({
        severity: 'success',
        summary: 'Atualizado',
        detail: 'Sua senha foi atualizada.',
        life: 3000,
    });
};
const updatePassword = () => {
    updatePasswordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            updatePasswordForm.reset();
            showSuccessToast();
        },
        onError: () => {
            if (updatePasswordForm.errors?.password) {
                updatePasswordForm.reset('password', 'password_confirmation');
                const newPasswordInputElement = newPasswordInput.value.$el.querySelector('input');
                if (newPasswordInputElement) {
                    newPasswordInputElement.focus();
                }
            }
            if (updatePasswordForm.errors?.current_password) {
                updatePasswordForm.reset('current_password');
                const currentPasswordInputElement = currentPasswordInput.value.$el.querySelector('input');
                if (currentPasswordInputElement) {
                    currentPasswordInputElement.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <InertiaHead title="Senha" />

        <SettingsLayout>
            <Card
                pt:body:class="max-w-2xl space-y-3"
                pt:caption:class="space-y-1"
            >
                <template #title>
                    Atualizar Senha
                </template>
                <template #subtitle>
                    Garanta que sua conta esteja usando uma senha segura
                </template>
                <template #content>
                    <form
                        class="space-y-6"
                        @submit.prevent="updatePassword"
                    >
                        <div class="flex flex-col gap-2">
                            <FloatLabel variant="in">
                                <Password
                                    ref="current-password-input"
                                    v-model="updatePasswordForm.current_password"
                                    :invalid="Boolean(updatePasswordForm.errors?.current_password)"
                                    :feedback="false"
                                    autocomplete="current-password"
                                    inputId="current-password"
                                    toggleMask
                                    required
                                    fluid
                                />
                                <label for="current-password">Senha atual</label>
                            </FloatLabel>
                            <Message
                                v-if="updatePasswordForm.errors?.current_password"
                                severity="error"
                                variant="simple"
                                size="small"
                            >
                                {{ updatePasswordForm.errors?.current_password }}
                            </Message>
                        </div>
                        <div class="flex flex-col gap-2">
                            <FloatLabel variant="in">
                                <Password
                                    ref="new-password-input"
                                    v-model="updatePasswordForm.password"
                                    :invalid="Boolean(updatePasswordForm.errors?.password)"
                                    autocomplete="new-password"
                                    inputId="password"
                                    toggleMask
                                    promptLabel="Escolha uma senha"
                                    weakLabel="Senha muito fraca"
                                    mediumLabel="Senha média"
                                    strongLabel="Senha forte"
                                    required
                                    fluid
                                />
                                <label for="password">Nova senha</label>
                            </FloatLabel>
                            <Message
                                v-if="updatePasswordForm.errors?.password"
                                severity="error"
                                variant="simple"
                                size="small"
                            >
                                {{ updatePasswordForm.errors?.password }}
                            </Message>
                        </div>
                        <div class="flex flex-col gap-2">
                            <FloatLabel variant="in">
                                <Password
                                    v-model="updatePasswordForm.password_confirmation"
                                    :invalid="Boolean(updatePasswordForm.errors?.password_confirmation)"
                                    :feedback="false"
                                    autocomplete="confirm-password"
                                    inputId="password-confirmation"
                                    toggleMask
                                    required
                                    fluid
                                />
                                <label for="password-confirmation">Confirme sua nova senha</label>
                            </FloatLabel>
                            <Message
                                v-if="updatePasswordForm.errors?.password_confirmation"
                                severity="error"
                                variant="simple"
                                size="small"
                            >
                                {{ updatePasswordForm.errors?.password_confirmation }}
                            </Message>
                        </div>
                        <UiButton
                            :loading="updatePasswordForm.processing"
                            type="submit"
                            label="Atualizar Senha"
                        />
                    </form>
                </template>
            </Card>
        </SettingsLayout>
    </AppLayout>
</template>
