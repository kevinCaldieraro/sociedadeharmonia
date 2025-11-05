<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestAuthLayout from '@/layouts/GuestAuthLayout.vue';
import UiButton from '@/components/ui/UiButton.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const emailInput = useTemplateRef('email-input');

const resetPwForm = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    resetPwForm.post(route('password.store'), {
        onFinish: () => resetPwForm.reset('password', 'password_confirmation'),
    });
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <GuestAuthLayout>
        <InertiaHead title="Redefina sua senha" />

        <template #title>
            <div class="text-center">
                Redefina sua senha
            </div>
        </template>

        <template #subtitle>
            <div class="text-center">
                Por favor digite sua nova senha abaixo
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
                        v-model="resetPwForm.email"
                        :invalid="Boolean(resetPwForm.errors?.email)"
                        type="email"
                        autocomplete="username"
                        required
                        fluid
                    />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message
                    v-if="resetPwForm.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ resetPwForm.errors?.email }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <FloatLabel variant="in">
                    <Password
                        v-model="resetPwForm.password"
                        :invalid="Boolean(resetPwForm.errors?.password)"
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
                    v-if="resetPwForm.errors?.password"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ resetPwForm.errors?.password }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <FloatLabel variant="in">
                    <Password
                        v-model="resetPwForm.password_confirmation"
                        :invalid="Boolean(resetPwForm.errors?.password_confirmation)"
                        :feedback="false"
                        autocomplete="new-password"
                        inputId="password-confirmation"
                        toggleMask
                        required
                        fluid
                    />
                    <label for="password-confirmation">Confirme sua nova senha</label>
                </FloatLabel>
                <Message
                    v-if="resetPwForm.errors?.password_confirmation"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ resetPwForm.errors?.password_confirmation }}
                </Message>
            </div>

            <div>
                <UiButton
                    :loading="resetPwForm.processing"
                    type="submit"
                    label="Redefinir senha"
                    fluid
                />
            </div>
        </form>
    </GuestAuthLayout>
</template>
