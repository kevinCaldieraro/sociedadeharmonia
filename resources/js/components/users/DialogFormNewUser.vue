<script setup>
import { computed } from 'vue';
import UiButton from '../ui/UiButton.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    userDialog: Boolean
});

const emit = defineEmits(['update:userDialog', 'save']);

const userDialogLocal = computed({
    get: () => props.userDialog,
    set: (value) => emit('update:userDialog', value)
});

const formRegisterUser = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const save = () => {
    formRegisterUser.clearErrors();

    emit('save', { ...formRegisterUser.data() }, (success, errors) => {
        if (success) {
            formRegisterUser.reset();
            emit('update:userDialog', false);
        }

        if (errors) {
            const formattedErrors = Object.fromEntries(
                Object.entries(errors).map(([key, value]) => [key, value.join(' ')])
            );

            formRegisterUser.setError(formattedErrors);
        }
    });
}

const close = () => {
    emit('update:userDialog', false);
    formRegisterUser.reset();
    formRegisterUser.clearErrors();
}
</script>

<template>
    <Dialog v-model:visible="userDialogLocal" :style="{ width: '450px' }" header="Cadastrar Novo Usuário" :modal="true" @hide="close">
        <form @submit.prevent="save" class="flex flex-col gap-6">
            <div>
                <FloatLabel variant="in">
                    <InputText
                        id="name"
                        type="text"
                        v-model.trim="formRegisterUser.name"
                        required
                        fluid
                        :invalid="Boolean(formRegisterUser.errors?.name)"
                    />
                    <label for="name">Nome</label>
                </FloatLabel>
                <Message
                    v-if="formRegisterUser.errors?.name"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formRegisterUser.errors?.name }}
                </Message>
            </div>
            <div>
                <FloatLabel variant="in">
                    <InputText
                        id="email"
                        type="email"
                        v-model.trim="formRegisterUser.email"
                        required
                        fluid
                        :invalid="Boolean(formRegisterUser.errors?.email)"
                    />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message
                    v-if="formRegisterUser.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formRegisterUser.errors?.email }}
                </Message>
            </div>
            <div>
                <FloatLabel variant="in">
                    <Password
                        v-model="formRegisterUser.password"
                        inputId="password"
                        toggleMask
                        fluid
                        promptLabel="Escolha uma senha"
                        weakLabel="Senha muito fraca"
                        mediumLabel="Senha média"
                        strongLabel="Senha forte"
                        required
                    />
                    <label for="password">Senha</label>
                </FloatLabel>
                <Message
                    v-if="formRegisterUser.errors?.password"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formRegisterUser.errors?.password }}
                </Message>
            </div>
            <div>
                <FloatLabel variant="in">
                    <Password
                        v-model="formRegisterUser.password_confirmation"
                        inputId="password_confirmation"
                        :feedback="false"
                        toggleMask
                        fluid
                        required
                    />
                    <label for="password_confirmation">Confirmar Senha</label>
                </FloatLabel>
                <Message
                    v-if="formRegisterUser.errors?.password_confirmation"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formRegisterUser.errors?.password_confirmation }}
                </Message>
            </div>
        </form>

        <template #footer>
            <UiButton :icon="X" label="Cancelar" text @click="close" />
            <UiButton :icon="Check" label="Cadastrar" @click="save" />
        </template>
    </Dialog>
</template>