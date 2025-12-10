<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import UiTittle from '@/components/ui/UiTittle.vue';
import UiButton from '@/components/ui/UiButton.vue';
import UiCard from '@/components/ui/UiCard.vue';
import { FilterMatchMode } from '@primevue/core/api';
import { computed, onMounted, ref } from 'vue';
import { BadgeCheck, Check, DollarSign, Search, X } from 'lucide-vue-next';
import { useForm, usePage } from '@inertiajs/vue3';
import { InputMask, InputText, Tag, useToast } from 'primevue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Loading from '@/components/utils/Loading.vue';

const monthsOptions = [
    { name: 'Janeiro', code: 1 },
    { name: 'Fevereiro', code: 2 },
    { name: 'Março', code: 3 },
    { name: 'Abril', code: 4 },
    { name: 'Maio', code: 5 },
    { name: 'Junho', code: 6 },
    { name: 'Julho', code: 7 },
    { name: 'Agosto', code: 8 },
    { name: 'Setembro', code: 9 },
    { name: 'Outubro', code: 10 },
    { name: 'Novembro', code: 11 },
    { name: 'Dezembro', code: 12 }
];
const statusOptions = [
    { name: 'Paga', code:'paga' },
    { name: 'Pendente', code:'pendente' },
    { name: 'Vencida', code:'vencida' },
    { name: 'Isenta', code:'isenta' }
];
const loading = ref(false);
const toast = useToast();
const registerPaymentDialog = ref(false);
const filters = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});
const page = usePage();
const selectedYear = ref(null);
const selectedMonths = ref([]);
const selectedStatus = ref([]);
const monthSubscriptions = ref([]);

const monthSubscriptionsPayeds = computed(() =>
  monthSubscriptions.value.filter(sub => sub.status === 'paga' || sub.status === 'isenta').length
);

const monthSubscriptionsPendents = computed(() =>
  monthSubscriptions.value.filter(sub => sub.status === 'pendente').length
);

const monthSubscriptionsExempts = computed(() =>
  monthSubscriptions.value.filter(sub => sub.status === 'vencida').length
);

const monthSubscriptionsRevenue = computed(() =>
  monthSubscriptions.value.filter(sub => sub.status === 'paga')
    .reduce((amount, currSub) => {
        return amount + Number(currSub.value)
    }, 0).toFixed(2)
);

const monthSubscriptionsPendentRevenue = computed(() =>
  (monthSubscriptions.value.filter(sub => sub.status === 'pendente').length * 79.9).toFixed(2)
);

const monthSubscriptionsExpiredRevenue = computed(() =>
  (monthSubscriptions.value.filter(sub => sub.status === 'vencida').length * 79.9).toFixed(2)
);

const monthSubscription = ref();
const formRegisterPayment = useForm({
    id: null,
    idSubscription: null,
    value: null,
    paid_at: null,
    payment_method: '',
    payment_proof_link: '',
});
const isPaydAtFocused = ref(false);
const isValuePaymentFocused = ref(false);

const openDialogRegisterPayment = (dataSubscription) => {
    const currDate = new Date();
    formRegisterPayment.paid_at = `${currDate.getDate()}/${currDate.getMonth() + 1}/${currDate.getFullYear()}`
    monthSubscription.value = dataSubscription;
    registerPaymentDialog.value = true;
};

const registerPayment = async () => {
    try {
        loading.value = true;
        formRegisterPayment.clearErrors();
        formRegisterPayment.id = monthSubscription.value.id;
        formRegisterPayment.idSubscription = monthSubscription.value.subscription.id;

        const response = await axios.post(route('subscriptions.registerPayment'), formRegisterPayment);
        const { subscriptionMonthMember, message } = response.data;
        loading.value = false;

        formRegisterPayment.reset();
        registerPaymentDialog.value = false;

        updateSubscriptionMonthStatus(subscriptionMonthMember);
        
        toast.add({ severity: 'success', summary: 'Sucesso', detail: message, life: 5000 });

    } catch (error) {
        loading.value = false;

        if (error.response?.status == 422) {
            const errors = error.response.data.errors;
            const formattedErrors = Object.fromEntries(
                Object.entries(errors).map(([key, value]) => [key, value.join(' ')])
            );

            formRegisterPayment.setError(formattedErrors);
            return false;
        }

        toast.add({ severity: 'error', summary: 'Erro', detail: 'Algo deu errado.', life: 5000 });
    }
};

const updateSubscriptionMonthStatus = (subscriptionMonthMember) => {
    const index = monthSubscriptions.value.findIndex(monthSub => monthSub.id === subscriptionMonthMember.id);

    if (index !== -1) {
        monthSubscriptions.value[index] = subscriptionMonthMember;
    }
}

const dateFormatter = (dateString) => {
  if (!dateString) return null;

  const [year, month, day] = dateString.split('-');
  return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
};

const moneyFormatter = (value) => {
    const formatted = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);

    return formatted
}

const getSeverityStatus = (status) => {
    switch (status) {
        case 'paga': return 'success';
        case 'pendente': return 'warn';
        case 'vencida': return 'danger';
        case 'isenta': return 'info';
    }
};

const search = () => {
    const months = selectedMonths.value.map(m => m.code);
    const status = selectedStatus.value.map(s => s.code);
    const year = !selectedYear.value ? 2025 : selectedYear.value;
    router.get(route('subscriptions.index'), { months, status, year });
}

const numberFormat = (value, decimals = 2, decPoint = ',', thousandsSep = '.') => {
    let n = Number(value).toFixed(decimals);
    let [intPart, decPart] = n.split('.');
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
    return intPart + decPoint + decPart;
}

const exemptMonth = async (id) => {
    try {
        loading.value = true;
        const response = await axios.post(route('subscriptions.exempt'), { id });
        const { subscriptionMonthMember, message } = response.data;
        loading.value = false;

        updateSubscriptionMonthStatus(subscriptionMonthMember);

        toast.add({ severity: 'success', summary: 'Sucesso', detail: message, life: 5000 });
    } catch(error) {
        loading.value = false;
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Algo deu errado.', life: 5000 });
    }
}

onMounted(() => {
    monthSubscriptions.value = page.props.monthSubscriptions;
    selectedMonths.value = monthsOptions.filter(month => page.props.months.includes(month.code));
    selectedStatus.value = statusOptions.filter(status => page.props.status.includes(status.code));
    selectedYear.value = page.props.year;
});
</script>

<template>
    <AppLayout>
        <InertiaHead title="Mensalidades" />
        <UiTittle :text="'\u{1F4B0} Mensalidades'" />

        <Loading :loading="loading" />

        <div class="grid gap-8 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
            <UiCard title="Mensalidades em dia" :value="monthSubscriptionsPayeds" :icon="DollarSign" colorIcon="text-green-500" colorIconBackground="bg-green-100" />
            <UiCard title="Mensalidades pendentes" :value="monthSubscriptionsPendents" :icon="DollarSign" colorIcon="text-orange-500" colorIconBackground="bg-orange-100" />
            <UiCard title="Mensalidades atrasadas" :value="monthSubscriptionsExempts" :icon="DollarSign" colorIcon="text-red-500" colorIconBackground="bg-red-100" />
            <UiCard title="Receita" :value="`R$ ${numberFormat(monthSubscriptionsRevenue)}`" :icon="DollarSign" colorIcon="text-green-500" colorIconBackground="bg-green-100" />
            <UiCard title="Receita pendente" :value="`R$ ${numberFormat(monthSubscriptionsPendentRevenue)}`" :icon="DollarSign" colorIcon="text-orange-500" colorIconBackground="bg-orange-100" />
            <UiCard title="Receita a pagar" :value="`R$ ${numberFormat(monthSubscriptionsExpiredRevenue)}`" :icon="DollarSign" colorIcon="text-red-500" colorIconBackground="bg-red-100" />
        </div>
        
        <DataTable
            ref="dt"
            :value="monthSubscriptions"
            dataKey="id"
            :paginator="true"
            :rows="10"
            :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Mostrando {first} de {last} de {totalRecords} Membros"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <div class="flex gap-2">
                        <InputText
                            v-model="selectedYear"
                            placeholder="Ano"
                            style="width: 120px;"
                            v-keyfilter.int
                        />
                        <MultiSelect
                            v-model="selectedMonths"
                            :options="monthsOptions"
                            :maxSelectedLabels="3"
                            :selectedItemsLabel="`${selectedMonths.length} Meses Selecionados`"
                            optionLabel="name"
                        />
                        <MultiSelect
                            v-model="selectedStatus"
                            :options="statusOptions"
                            :maxSelectedLabels="3"
                            :selectedItemsLabel="'Todos Status'"
                            optionLabel="name"
                        />
                        <UiButton label="Pesquisar" severity="info" @click="search" />
                    </div>
                    <IconField>
                        <InputIcon>
                            <Search/>
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                    </IconField>
                </div>
            </template>
            <Column field="subscription.member.name" header="Membro" style="max-width: 10rem"></Column>
            <Column field="month" header="Mês" style="max-width: 8rem">
                <template #body="slotProps">
                    {{ monthsOptions[slotProps.data.month - 1].name }}
                </template>
            </Column>
            <Column field="expiration_date" header="Vencimento" style="max-width: 8rem">
                <template #body="slotProps">
                    {{ dateFormatter(slotProps.data.expiration_date) }}
                </template>
            </Column>
            <Column field="paid_at" header="Pago em" style="max-width: 8rem">
                <template #body="slotProps">
                    {{ dateFormatter(slotProps.data.paid_at) }}
                </template>
            </Column>
            <Column field="paid_value" header="Valor Pago" style="max-width: 8rem">
                <template #body="slotProps">
                    {{ moneyFormatter(slotProps.data.value) }}
                </template>
            </Column>
            <Column field="status" header="Status" style="max-width: 6rem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.status" :severity="getSeverityStatus(slotProps.data.status)" />
                </template>
            </Column>
            <Column header="Ações" style="max-width: 6rem">
                <template #body="slotProps">
                    <div>
                        <UiButton
                            class="mr-1"
                            :icon="DollarSign"
                            severity="success"
                            variant="outlined"
                            v-tooltip.bottom="{ value: 'Registrar pagamento', showDelay: 100, hideDelay: 100 }"
                            rounded
                            @click="openDialogRegisterPayment(slotProps.data)"
                            :disabled="slotProps.data.status === 'isenta' || slotProps.data.status === 'paga'"
                        />
                        <UiButton
                            :icon="BadgeCheck"
                            severity="info"
                            variant="outlined"
                            v-tooltip.bottom="{ value: 'Isentar mês', showDelay: 100, hideDelay: 100 }"
                            rounded
                            @click="exemptMonth(slotProps.data.id)"
                            :disabled="slotProps.data.status === 'isenta' || slotProps.data.status === 'paga'"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog
            v-model:visible="registerPaymentDialog"
            :style="{ width: '400px' }"
            :modal="true"
            header="Registrar Pagamento"
            @hide="formRegisterPayment.reset()"
        >
            <p class="mb-4 text-gray-500"><b>Membro:</b> {{ monthSubscription.subscription.member.name }}</p>
            <form @submit.prevent="registerPayment" class="flex flex-col gap-6">
                <div>
                    <FloatLabel variant="in">
                        <InputMask
                            id="paid_at"
                            v-model="formRegisterPayment.paid_at"
                            mask="99/99/9999"
                            slotChar="dd/mm/yyyy"
                            :invalid="Boolean(formRegisterPayment.errors?.paid_at)"
                            fluid
                            @focus="isPaydAtFocused = true"
                            @blur="isPaydAtFocused = false"
                            :placeholder="isPaydAtFocused ? 'dd/mm/yyyy' : null"
                        />
                        <label for="paid_at">Data do Pagamento</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegisterPayment.errors?.paid_at"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formRegisterPayment.errors?.paid_at }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputNumber
                            v-model="formRegisterPayment.value"
                            inputId="value"
                            mode="currency"
                            currency="BRL"
                            @focus="isValuePaymentFocused = true"
                            @blur="isValuePaymentFocused = false"
                            :placeholder="isValuePaymentFocused ? 'R$ ' : null"
                            required
                            :invalid="Boolean(formRegisterPayment.errors?.value)"
                            fluid
                        />
                        <label for="value">Valor</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegisterPayment.errors?.value"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formRegisterPayment.errors?.value }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            v-model="formRegisterPayment.payment_method"
                            id="payment_method"
                            required
                            :invalid="Boolean(formRegisterPayment.errors?.payment_method)"
                            fluid
                        />
                        <label for="payment_method">Forma de Pagamento</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegisterPayment.errors?.payment_method"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2 ml-2"
                    >
                        {{ formRegisterPayment.errors?.payment_method }}
                    </Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            v-model="formRegisterPayment.payment_proof_link"
                            id="payment_proof_link"
                            required
                            fluid
                        />
                        <label for="payment_proof_link">Link do Comprovante (opcional)</label>
                    </FloatLabel>
                </div>
            </form>
            <template #footer>
                <UiButton :icon="X" label="Cancelar" text @click="registerPaymentDialog = false" />
                <UiButton :icon="Check" label="Registrar" @click="registerPayment" />
            </template>
        </Dialog>
    </AppLayout>
</template>