<script setup>
import { ref, onMounted, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import UiButton from '@/components/ui/UiButton.vue';
import UiTittle from '@/components/ui/UiTittle.vue';
import { UserPlus, Search, UserPen, TriangleAlert, X, Check, Shield, Heart, Plus, Download, LockKeyhole, LockKeyholeOpen } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import Loading from '@/components/utils/Loading.vue';
import axios from 'axios';
import UiCard from '@/components/ui/UiCard.vue';
import DialogFormNewMember from '@/components/members/DialogFormNewMember.vue';
import DialogFormEditMember from '@/components/members/DialogFormEditMember.vue';

const toast = useToast();
const page = usePage();
const dt = ref();
const loading = ref(false);
const messageLoading = ref('');
const members = ref([]);
const patrimonialMembers = computed(() =>
    members.value.filter(m => m.role.type === 'patrimonial').length
);
const patrimonialSpouseMembers = computed(() =>
    members.value.filter(m => m.role.type === 'patrimonial_spouse').length
);
const affiliatedMembers = computed(() =>
    members.value.filter(m => m.role.type === 'affiliated').length
);
const newMemberDialog = ref(false);
const editMemberDialog = ref(false);
const disableMemberDialog = ref(false);
const member = ref({});
const filters = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});

const openNewMemberDialog = () => {
    member.value = {};
    newMemberDialog.value = true;
};

const openEditMemberDialog = (memberData) => {
    member.value = memberData;
    editMemberDialog.value = true;
};

const clearMemberData = () => {
    member.value = {};
}

const confirmDisableMember = (memberData) => {
    member.value = memberData;
    disableMemberDialog.value = true;
};

const disableMember = async () => {
    handleStatusMember('members.disable', 'Desativado');
};

const enableMember = async () => {
    handleStatusMember('members.enable', 'Ativado');
};

const handleStatusMember = async (routeString, summaryToast) => {
    try {
        loading.value = true;
        disableMemberDialog.value = false;
        const res = await axios.patch(route(routeString, { id: member.value.id }));
        const { updatedMembers, message } = res.data;
        loading.value = false;

        updateMembers(updatedMembers);

        toast.add({ severity: 'success', summary: summaryToast, detail: message, life: 5000 });

    } catch (error) {
        loading.value = false;
        toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message, life: 5000 })
    }
}

const updateMembers = (membersData) => {
    membersData.forEach(memberData => {
        const index = members.value.findIndex(m => m.id === memberData.id);
    
        if (index !== -1) {
            members.value[index] = memberData;
        }
    });
}

const exportCSV = () => {
    dt.value.exportCSV();
};

const getSeverityStatus = (status) => {
    switch (status) {
        case 'ativo': return 'success';
        case 'desativado': return 'warn';
    }
};

const getSeverityStatusSubscription = (status) => {
    switch (status) {
        case 'regular': return 'success';
        case 'irregular': return 'danger';
    }
};

const getTypeMember = (type) => {
    switch (type) {
        case 'patrimonial': return 'Patrimonial';
        case 'patrimonial_spouse': return 'Cônjuge';
        case 'affiliated': return 'Agregado';
    }
};

const getSeverityExempt = (isExempt) => {
    if (isExempt) return 'success';

    return 'danger';
};

const editMember = async (formData, done) => {
    try {
        loading.value = true;
        const response = await axios.patch(route('members.update', member.value.id), formData);
        const { memberUpdated, message } = response.data;
        loading.value = false;

        const index = members.value.findIndex(m => m.id === memberUpdated.id);

        if (index !== -1) {
            members.value[index] = memberUpdated;
        }

        toast.add({ severity: 'success', summary: 'Sucesso', detail: message, life: 5000 });
        done(true);

    } catch (error) {
        loading.value = false;
        if (error.response?.status == 422) {
            done(false, error.response.data.errors);
            return false;
        }

        toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message, life: 5000 });
        done(false);
    }
};

const registerMember = async (formData, done) => {
    try {
        loading.value = true;
        const response = await axios.post(route('members.store'), formData);
        const { member, message } = response.data;
        members.value.push(member)
        loading.value = false;

        toast.add({ severity: 'success', summary: 'Sucesso', detail: message, life: 5000 });
        done(true);

    } catch (error) {
        loading.value = false;
        if (error.response?.status == 422) {
            done(false, error.response.data.errors);
            return false;
        }

        toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message, life: 5000 });
        done(false);
    }
};

const onFileSelect = async (event) => {
    const file = event.files[0];
    const formData = new FormData();
    formData.append("member-sheet", file);

    messageLoading.value = 'Importando membros...';
    loading.value = true;

    try {
        const response = await axios.post(route('members.import'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        const { message, membersData } = response.data;

        members.value = membersData;
        loading.value = false;
        messageLoading.value = '';

        toast.add({
            severity: 'success',
            summary: 'Importação concluída',
            detail: message,
            life: 5000,
        });

    } catch (error) {
        loading.value = false;
        messageLoading.value = '';

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: error.response?.data?.message || error.message,
            life: 5000,
        });
    }
};

onMounted(() => {
    members.value = page.props.members;
});

</script>

<template>
    <AppLayout>
        <InertiaHead title="Membros" />

        <UiTittle :text="'\u{1F465} Membros'" />
        <Loading :loading="loading" :message="messageLoading" />
        <div class="card">
            <div class="grid gap-8 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 mb-6">
                <UiCard title="Patrimoniais" :value="patrimonialMembers" :icon="Shield" colorIcon="text-orange-500" colorIconBackground="bg-orange-100" />
                <UiCard title="Cônjuges" :value="patrimonialSpouseMembers" :icon="Heart" colorIcon="text-red-500" colorIconBackground="bg-red-100" />
                <UiCard title="Agregados" :value="affiliatedMembers" :icon="UserPlus" colorIcon="text-sky-500" colorIconBackground="bg-sky-100" />
            </div>
            <Toolbar class="mb-4">
                <template #start>
                    <UiButton label="Novo Membro" :icon="Plus" class="mr-2" @click="openNewMemberDialog" />
                </template>

                <template #end>
                    <FileUpload
                        name="member-sheet"
                        mode="basic"
                        auto
                        accept=".csv,.xlsx"
                        :maxFileSize="1000000"
                        chooseLabel="Importar"
                        class="mr-2"
                        :chooseButtonProps="{ severity: 'secondary' }"
                        @select="onFileSelect"
                        :fileName="false"
                    >
                    </FileUpload>
                    <UiButton label="Exportar" :icon="Download" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <p v-if="members.length === 0">Não há membros cadastrados.</p>
            <DataTable
                v-if="members.length > 0"
                ref="dt"
                :value="members"
                dataKey="id"
                :paginator="true"
                :rows="5"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Mostrando {first} de {last} de {totalRecords} membros"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <IconField>
                            <InputIcon>
                                <Search/>
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                        </IconField>
                    </div>
                </template>

                <Column field="name" header="Nome" sortable style="max-width: 10rem"></Column>
                <Column field="role.type" header="Tipo" sortable style="max-width: 6rem">
                    <template #body="slotProps">
                        {{ getTypeMember(slotProps.data.role.type) }}
                    </template>
                </Column>
                <Column field="role.patrimonial_member.name" header="Vinculado a" sortable style="max-width: 10rem"></Column>
                <Column field="role.relationship" header="Parentesco" sortable style="max-width: 6rem"></Column>
                <Column field="role.is_exempt" header="Isento" sortable style="max-width: 4rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.role.is_exempt ? 'sim' : 'não'" :severity="getSeverityExempt(slotProps.data.role.is_exempt)" />
                    </template>
                </Column>
                <Column field="role.status" header="Status" sortable style="max-width: 6rem">
                    <template #body="slotProps">
                        <Tag
                            :value="slotProps.data.role.status"
                            :severity="getSeverityStatus(slotProps.data.role.status)"
                        />
                    </template>
                </Column>
                <Column field="status" header="Situação" sortable style="max-width: 6rem">
                    <template #body="slotProps">
                        <Tag
                            :value="slotProps.data.subscription?.status ?? slotProps.data.role.patrimonial_member?.subscription.status"
                            :severity="getSeverityStatusSubscription(slotProps.data.subscription?.status ?? slotProps.data.role.patrimonial_member?.subscription.status)"
                        />
                    </template>
                </Column>
                <Column :exportable="false" style="max-width: 8rem">
                    <template #body="slotProps">
                        <UiButton
                            :icon="UserPen"
                            variant="outlined"
                            rounded
                            class="mr-2"
                            @click="openEditMemberDialog(slotProps.data)"
                            v-tooltip.bottom="{ value: 'Editar', showDelay: 100, hideDelay: 100 }"
                        />
                        <UiButton
                            :icon="slotProps.data.role.status === 'ativo' ? LockKeyholeOpen : LockKeyhole"
                            variant="outlined"
                            rounded
                            :severity="slotProps.data.role.status === 'ativo' ? 'success' : 'warn'"
                            @click="confirmDisableMember(slotProps.data)"
                            v-tooltip.bottom="{ value: slotProps.data.role.status === 'ativo' ? 'Desativar' : 'Ativar', showDelay: 100, hideDelay: 100 }"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>

        <DialogFormNewMember v-model:memberDialog="newMemberDialog" @save="registerMember" />
        <DialogFormEditMember
            v-model:memberDialog="editMemberDialog"
            v-model:memberData="member"
            @resetMemberData="clearMemberData"
            @save="editMember"
        />

        <Dialog v-model:visible="disableMemberDialog" :style="{ width: '450px' }" header="Atenção" :modal="true">
            <div class="flex items-center gap-4">
                <TriangleAlert class="w-10! h-10!" />
                <span v-if="member && member.role.status === 'ativo'">
                    Você tem certeza que quer desativar o membro <b>{{ member.name }}</b> da Associação?
                </span>
                <span v-if="member && member.role.status === 'desativado'">
                    Você tem certeza que quer ativar o membro <b>{{ member.name }}</b> da Associação?
                </span>
            </div>
            <template #footer>
                <UiButton label="Não" :icon="X" text @click="disableMemberDialog = false" severity="secondary" variant="text" />
                <UiButton label="Sim" :icon="Check" @click="member.role.status === 'ativo' ? disableMember() : enableMember()" severity="danger" />
            </template>
        </Dialog>
	</AppLayout>
</template>
