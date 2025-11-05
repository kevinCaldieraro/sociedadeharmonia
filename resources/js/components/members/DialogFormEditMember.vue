<script setup>
import { Check, X } from 'lucide-vue-next';
import { computed, ref, watch, watchEffect } from 'vue';
import UiButton from '@/components/ui/UiButton.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    memberDialog: Boolean,
    memberData: Object,
});
const emit = defineEmits([
    'update:memberDialog',
    'save',
    'resetMemberData',
]);

const memberDialogLocal = computed({
    get: () => props.memberDialog,
    set: (value) => emit('update:memberDialog', value)
});

const typeMembersOptions = [
    { name: 'Patrimonial', code: 'patrimonial' },
    { name: 'Cônjuge', code: 'patrimonial_spouse' },
    { name: 'Agregado', code: 'affiliated' },
];
const isPatrimonialMemberIdFocused = ref(false);
const isPatrimonialPurchasedDateFocused = ref(false);
const isPatrimonialValueFocused = ref(false);
const isBirthDateFocused = ref(false);
const isJoinDateFocused = ref(false);
const isRelationshipFocused = ref(false);
const filteredPatrimonialMembers = ref([]);
const patrimonialMembersOptions = ref([]);
const formMember = useForm({
    name: '',
    cpf: '',
    birth_date: '',
    email: '',
    phone: '',
    type_member: '',
    patrimonial_purchase_date: '',
    join_date: '',
    patrimonial_value: '',
    relationship: '',
    patrimonial_member: '',
    street: '',
    number: '',
    neighborhood: '',
    city: ''
});

const close = () => {
    emit('update:memberDialog', false);
    emit('resetMemberData');
    formMember.reset();
    formMember.clearErrors();
}

const searchPatrimonialMember = (event) => {
    if (event.query.trim().length > 0) {
        filteredPatrimonialMembers.value = patrimonialMembersOptions.value.filter(patMemb => {
            return patMemb.name.toLowerCase().startsWith(event.query.toLowerCase());
        })
    }
}

const save = () => {
    formMember.clearErrors();

    emit('save', { ...formMember.data() }, (success, errors) => {
        if (success) {
            formMember.reset();
            emit('update:memberDialog', false);
        }

        if (errors) {
            const formattedErrors = Object.fromEntries(
                Object.entries(errors).map(([key, value]) => [key, value.join(' ')])
            );

            formMember.setError(formattedErrors);
        }
    });
}

const dateFormatter = (dateString) => {
  if (!dateString) return null;

  const [year, month, day] = dateString.split('-');
  return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
};

watchEffect(async () => {
    if (
        (formMember.type_member === 'patrimonial_spouse' || formMember.type_member === 'affiliated')
        && patrimonialMembersOptions.value.length === 0
    ) {
        const response = await axios.get(route('members.getPatrimonialMembers'));
        const { patrimonialMembers } = response.data;

        patrimonialMembersOptions.value = patrimonialMembers;
    }
});

watch(
    () => props.memberData,
    (member) => {
        if (member) {
            formMember.name = member.name || '';
            formMember.cpf = member.cpf || '';
            formMember.birth_date = dateFormatter(member.birth_date) || '';
            formMember.email = member.email || '';
            formMember.phone = member.phone || '';
            formMember.type_member = member.role?.type || '';
            formMember.patrimonial_purchase_date = dateFormatter(member.role?.patrimonial_purchase_date) || '';
            formMember.join_date = dateFormatter(member.role?.join_date) || '';
            formMember.patrimonial_value = member.role?.patrimonial_value || '';
            formMember.relationship = member.role?.relationship || '';
            formMember.patrimonial_member = member.role?.patrimonial_member || '';
            formMember.street = member.street || '';
            formMember.number = member.number || '';
            formMember.neighborhood = member.neighborhood || '';
            formMember.city = member.city || '';
        } else {
            formMember.reset();
        }
    },
    { immediate: true }
);
</script>

<template>
    <Dialog v-model:visible="memberDialogLocal" :style="{ width: '700px' }" :modal="true" :blockScroll="false" @hide="close">
        <template #header>
            <div class="flex flex-col">
                <h2 class="text-2xl font-black mb-2">Editar Membro</h2>
                <p class="text-gray-500">Atualize as informações do membro no sistema</p>
            </div>
        </template>
        <form @submit.prevent="registerMember" class="flex flex-col gap-6 mt-2">
            <h3 class="text-xl font-black">Dados do Membro</h3>
            <div>
                <FloatLabel variant="in">
                    <InputText
                        id="name"
                        v-model.trim="formMember.name"
                        :invalid="Boolean(formMember.errors?.name)"
                        fluid
                    />
                    <label for="name">Nome Completo</label>
                </FloatLabel>
                <Message
                    v-if="formMember.errors?.name"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formMember.errors?.name }}
                </Message>
            </div>
            <div>
                <FloatLabel variant="in">
                    <InputText
                        id="email"
                        v-model="formMember.email"
                        type="email"
                        :invalid="Boolean(formMember.errors?.email)"
                        required
                        fluid
                    />
                    <label for="email">Email</label>
                </FloatLabel>
                <Message
                    v-if="formMember.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formMember.errors?.email }}
                </Message>
            </div>
            <div class="grid gap-6 grid-cols-2">
                <div>
                    <FloatLabel variant="in">
                        <InputMask
                            id="cpf"
                            v-model="formMember.cpf"
                            mask="999.999.999-99"
                            :invalid="Boolean(formMember.errors?.cpf)"
                            fluid
                        />
                        <label for="cpf">CPF</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.cpf"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.cpf }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputMask
                            id="birth_date"
                            v-model="formMember.birth_date"
                            mask="99/99/9999"
                            :placeholder="isBirthDateFocused ? 'dd/mm/yyyy' : null"
                            @focus="isBirthDateFocused = true"
                            @blur="isBirthDateFocused = false"
                            slotChar="dd/mm/yyyy"
                            :invalid="Boolean(formMember.errors?.birth_date)"
                            fluid
                            disabled
                        />
                        <label for="birth_date">Data de Nascimento</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.birth_date"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.birth_date }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputMask
                            id="phone"
                            v-model="formMember.phone"
                            mask="(99) 99999-9999"
                            :invalid="Boolean(formMember.errors?.phone)"
                            fluid=""
                        />
                        <label for="phone">Telefone</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.phone"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.phone }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputMask
                            id="join_date"
                            v-model="formMember.join_date"
                            mask="99/99/9999"
                            :placeholder="isJoinDateFocused ? 'dd/mm/yyyy' : null"
                            @focus="isJoinDateFocused = true"
                            @blur="isJoinDateFocused = false"
                            slotChar="dd/mm/yyyy"
                            :invalid="Boolean(formMember.errors?.join_date)"
                            fluid
                            disabled
                        />
                        <label for="join_date">Data de Adesão</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.join_date"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.join_date }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <Select
                            id="type_member"
                            v-model="formMember.type_member"
                            :options="typeMembersOptions"
                            optionLabel="name"
                            optionValue="code"
                            :invalid="Boolean(formMember.errors?.type_member)"
                            fluid
                            disabled
                        />
                        <label for="type_member">Tipo do Membro</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.type_member"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.type_member }}
                    </Message>
                </div>
                <div v-if="formMember.type_member === 'patrimonial'">
                    <FloatLabel variant="in">
                        <InputMask
                            id="patrimonial_purchase_date"
                            v-model="formMember.patrimonial_purchase_date"
                            mask="99/99/9999"
                            :placeholder="isPatrimonialPurchasedDateFocused ? 'dd/mm/yyyy' : null"
                            @focus="isPatrimonialPurchasedDateFocused = true"
                            @blur="isPatrimonialPurchasedDateFocused = false"
                            slotChar="dd/mm/yyyy"
                            :invalid="Boolean(formMember.errors?.patrimonial_purchase_date)"
                            fluid
                            disabled
                        />
                        <label for="patrimonial_purchase_date">Data de Compra do Título</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.patrimonial_purchase_date"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.patrimonial_purchase_date }}
                    </Message>
                </div>
                <div v-if="formMember.type_member === 'patrimonial'">
                    <FloatLabel variant="in">
                        <InputNumber
                            id="patrimonial_value"
                            v-model="formMember.patrimonial_value"
                            mode="currency"
                            currency="BRL"
                            @focus="isPatrimonialValueFocused = true"
                            @blur="isPatrimonialValueFocused = false"
                            :placeholder="isPatrimonialValueFocused ? 'R$ ' : null"
                            required
                            fluid
                            disabled
                        />
                        <label for="patrimonial_value">Valor do Título</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.patrimonial_value"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.patrimonial_value }}
                    </Message>
                </div>
                <div v-if="formMember.type_member === 'affiliated' || formMember.type_member === 'patrimonial_spouse'">
                    <FloatLabel variant="in">
                        <InputText
                            id="relationship"
                            v-model="formMember.relationship"
                            fluid
                            :placeholder="isRelationshipFocused ? 'Ex.: Esposa, Filho, Neto, ...' : null"
                            @focus="isRelationshipFocused = true"
                            @blur="isRelationshipFocused = false"
                            :invalid="Boolean(formMember.errors?.relationship)"
                            disabled
                        />
                        <label for="relationship">Parentesco</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.relationship"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.relationship }}
                    </Message>
                </div>
            </div>
            <div v-if="formMember.type_member === 'affiliated' || formMember.type_member === 'patrimonial_spouse'">
                <FloatLabel variant="in">
                    <AutoComplete
                        id="patrimonial_member"
                        v-model="formMember.patrimonial_member"
                        optionLabel="name"
                        :suggestions="filteredPatrimonialMembers"
                        @complete="searchPatrimonialMember"
                        @focus="isPatrimonialMemberIdFocused = true"
                        @blur="isPatrimonialMemberIdFocused = false"
                        :placeholder="isPatrimonialMemberIdFocused ? 'Busque um nome...' : null"
                        :invalid="Boolean(formMember.errors?.patrimonial_member)"
                        emptySearchMessage="Nenhum resultado encontrado"
                        fluid
                        disabled
                    />
                    <label for="patrimonial_member">Membro Patrimonial</label>
                </FloatLabel>
                <Message
                    v-if="formMember.errors?.patrimonial_member"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2 ml-2"
                >
                    {{ formMember.errors?.patrimonial_member }}
                </Message>
            </div>
            <h3 class="text-xl font-black">Endereço</h3>
            <div class="grid gap-6 grid-cols-2">
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            id="street"
                            v-model="formMember.street"
                            :invalid="Boolean(formMember.errors?.street)"
                            fluid
                        />
                        <label for="street">Rua</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.street"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.street }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            id="number"
                            v-model="formMember.number"
                            :invalid="Boolean(formMember.errors?.number)"
                            fluid
                        />
                        <label for="street">Número</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.number"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.number }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            id="neighborhood"
                            v-model="formMember.neighborhood"
                            :invalid="Boolean(formMember.errors?.neighborhood)"
                            fluid
                        />
                        <label for="street">Bairro</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.neighborhood"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.neighborhood }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            id="city"
                            v-model="formMember.city"
                            :invalid="Boolean(formMember.errors?.city)"
                            fluid
                        />
                        <label for="street">Cidade</label>
                    </FloatLabel>
                    <Message
                        v-if="formMember.errors?.city"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formMember.errors?.city }}
                    </Message>
                </div>
            </div>
        </form>
        <template #footer>
            <UiButton label="Cancelar" :icon="X" text @click="close" />
            <UiButton label="Atualizar" :icon="Check" @click="save" />
        </template>
    </Dialog>
</template>