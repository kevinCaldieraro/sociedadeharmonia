<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/UserSettingsLayout.vue';
import DeleteUserModal from '@/components/DeleteUserModal.vue';
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
    { label: 'Perfil' },
];

const deleteUserModalOpen = ref(false);

const user = usePage().props.auth.user;
const toast = useToast();
const updateProfileForm = useForm({
    name: user.name,
    email: user.email,
});

const sendVerificationForm = useForm({});
const sendEmailVerification = () => {
    sendVerificationForm.post(route('verification.send'));
};

const showSuccessToast = () => {
    toast.add({
        severity: 'success',
        summary: 'Salvo',
        detail: 'As informações do perfil foram atualizadas.',
        life: 3000,
    });
};
const updateProfileInformation = () => {
    updateProfileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessToast();
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <InertiaHead title="Perfil" />

        <SettingsLayout>
            <div class="space-y-4 md:space-y-8">
                <Card
                    pt:body:class="max-w-2xl space-y-3"
                    pt:caption:class="space-y-1"
                >
                    <template #title>
                        Informações do Perfil
                    </template>
                    <template #subtitle>
                        Atualize seu nome e endereço de email
                    </template>
                    <template #content>
                        <form
                            class="space-y-6"
                            @submit.prevent="updateProfileInformation"
                        >
                            <div class="flex flex-col gap-2">
                                <FloatLabel variant="in">
                                    <InputText
                                        id="name"
                                        v-model="updateProfileForm.name"
                                        :invalid="Boolean(updateProfileForm.errors?.name)"
                                        type="text"
                                        autocomplete="name"
                                        required
                                        fluid
                                    />
                                    <label for="name">Nome</label>
                                </FloatLabel>
                                <Message
                                    v-if="updateProfileForm.errors?.name"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ updateProfileForm.errors?.name }}
                                </Message>
                            </div>
                            <div class="flex flex-col gap-2">
                                <FloatLabel variant="in">
                                    <InputText
                                        id="email"
                                        v-model="updateProfileForm.email"
                                        :invalid="Boolean(updateProfileForm.errors?.email)"
                                        type="email"
                                        autocomplete="username"
                                        required
                                        fluid
                                    />
                                    <label for="email">Email</label>
                                </FloatLabel>
                                <Message
                                    v-if="updateProfileForm.errors?.email"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ updateProfileForm.errors?.email }}
                                </Message>
                            </div>
                            <UiButton
                                :loading="updateProfileForm.processing"
                                type="submit"
                                label="Atualizar"
                            />
                        </form>
                    </template>
                </Card>
                <Card
                    pt:body:class="max-w-2xl space-y-3"
                    pt:caption:class="space-y-1"
                    v-if="!user.is_admin"
                >
                    <template #title>
                        Excluir Conta
                    </template>
                    <template #subtitle>
                        Exclua sua conta e informações pessoais
                    </template>
                    <template #content>
                        <DeleteUserModal v-model="deleteUserModalOpen" />
                        <Message
                            severity="error"
                            pt:root:class="p-2"
                        >
                            <div class="flex flex-col gap-4">
                                <div>
                                    <div class="text-lg">
                                        Atenção
                                    </div>
                                    <div>
                                        Por favor prossiga com cuidado, essa ação não pode ser desfeita.
                                    </div>
                                </div>
                                <div>
                                    <UiButton
                                        label="Excluir conta"
                                        severity="danger"
                                        @click="deleteUserModalOpen = true"
                                    />
                                </div>
                            </div>
                        </Message>
                    </template>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
