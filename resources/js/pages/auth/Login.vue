<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestAuthLayout from '@/layouts/GuestAuthLayout.vue';
import UiButton from '@/components/ui/UiButton.vue';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const emailInput = useTemplateRef('email-input');

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <GuestAuthLayout>
        <InertiaHead title="Entrar" />

        <template
            v-if="props.status"
            #message
        >
            <Message
                severity="success"
                :closable="false"
                class="shadow-sm"
            >
                {{ props.status }}
            </Message>
        </template>

        <template #title>
            <div class="text-center">
                Faça login na sua conta
            </div>
        </template>

        <template #subtitle>
            <div class="text-center">
                Preencha seu email e senha abaixo para entrar
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
                        v-model="loginForm.email"
                        :invalid="Boolean(loginForm.errors?.email)"
                        type="email"
                        autocomplete="username"
                        required
                        fluid
                    />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message
                    v-if="loginForm.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ loginForm.errors?.email }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-end">
                    <InertiaLink
                        v-if="props.canResetPassword"
                        :href="route('password.request')"
                    >
                        <UiButton
                            class="p-0"
                            variant="link"
                            label="Esqueceu sua senha?"
                        />
                    </InertiaLink>
                </div>
                <FloatLabel variant="in">
                    <Password
                        v-model="loginForm.password"
                        :invalid="Boolean(loginForm.errors?.password)"
                        :feedback="false"
                        autocomplete="current-password"
                        inputId="password"
                        toggleMask
                        required
                        fluid
                    />
                    <label for="password">Senha</label>
                </FloatLabel>
                <Message
                    v-if="loginForm.errors?.password"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ loginForm.errors?.password }}
                </Message>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <Checkbox
                            v-model="loginForm.remember"
                            class="mr-2"
                            inputId="remember"
                            :binary="true"
                        />
                        <label for="remember">Lembre-me</label>
                    </div>
                </div>
            </div>

            <div>
                <UiButton
                    :loading="loginForm.processing"
                    type="submit"
                    label="Entrar"
                    fluid
                />
            </div>
        </form>
    </GuestAuthLayout>
</template>
