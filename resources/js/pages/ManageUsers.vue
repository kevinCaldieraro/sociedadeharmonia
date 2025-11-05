<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { Toolbar, useToast, Tag } from 'primevue';
import { Check, UserX, X, TriangleAlert, ShieldUser, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import UiButton from '@/components/ui/UiButton.vue';
import UiTittle from '@/components/ui/UiTittle.vue';
import DialogFormNewUser from '@/components/users/DialogFormNewUser.vue';
import { usePage } from '@inertiajs/vue3';
import Loading from '@/components/utils/Loading.vue';

const page = usePage();
const userLogged = page.props.auth.user;
const loading = ref(false);
const toast = useToast();
const user = ref({});
const users = ref([]);
const dialogNewUser = ref(false);
const dialogDeleteUser = ref(false);

const openDialogNewUser = () => {
    dialogNewUser.value = true;   
}

const getUserType = (dataUser) => {
    if (dataUser.is_admin) {
        return 'Administrador';
    }

    return 'Usuário Padrão';
}

const getUserSeverity = (dataUser) => {
    if (dataUser.is_admin) {
        return 'info';
    }

    return 'success';
}

const registerUser = async (formData, done) => {
    try {
        loading.value = true;
        const res = await axios.post(route('users.store'), formData);
        const { newUser, message } = res.data;
        loading.value = false;

        users.value.push(newUser);
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
}

const confirmDeleteUser = (userData) => {
    user.value = userData;
    dialogDeleteUser.value = true;
}

const deleteUser = async () => {
    try {
        loading.value = true;
        const res = await axios.delete(route('users.destroy', { user: user.value.id }));
        const { id, message } = res.data;
        loading.value = false;

        users.value = users.value.filter(user => user.id !== id);
        toast.add({ severity: 'success', summary: 'Excluído', detail: message, life: 5000 });
        dialogDeleteUser.value = false;

    } catch (error) {
        loading.value = false;
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Algo deu errado.', life: 5000 });
    }
}

onMounted(() => {
    users.value = page.props.users;
});
</script>

<template>
    <AppLayout>
        <InertiaHead title="Usuários do Sistema" />

        <UiTittle :text="'\u{1F464} Usuários do Sistema'" />

        <Loading :loading="loading" />
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <UiButton label="Novo Usuário" :icon="Plus" class="mr-2" @click="openDialogNewUser" />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                :value="users"
                dataKey="id"
                :paginator="true"
                :rows="10"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Mostrando {first} de {last} de {totalRecords} usuários"
            >
                <Column field="name" header="Nome" style="max-width: 12rem"></Column>
                <Column field="email" header="Email" style="max-width: 10rem"></Column>
                <Column field="is_admin" header="Tipo" style="max-width: 6rem">
                    <template #body="slotProps">
                        <Tag :severity="getUserSeverity(slotProps.data)">
                            <ShieldUser v-if="slotProps.data.is_admin" />
                            <span>{{ getUserType(slotProps.data) }}</span>
                        </Tag>
                    </template>
                </Column>
                <Column header="Ações" style="max-width: 6rem">
                    <template #body="slotProps">
                        <UiButton v-if="slotProps.data.id !== userLogged.id" :icon="UserX" variant="outlined" rounded severity="danger" @click="confirmDeleteUser(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <DialogFormNewUser v-model:userDialog="dialogNewUser" @save="registerUser" />

        <Dialog v-model:visible="dialogDeleteUser" :style="{ width: '450px' }" header="Atenção" :modal="true">
            <div class="flex items-center gap-4">
                <TriangleAlert class="w-10! h-10!" />
                <span v-if="user">Você tem certeza que deseja excluir o usuário <b>{{ user.name }}</b>?</span>
            </div>
            <template #footer>
                <UiButton label="Não" :icon="X" text @click="dialogDeleteUser = false" severity="secondary" variant="text" />
                <UiButton label="Sim" :icon="Check" @click="deleteUser" severity="danger" />
            </template>
        </Dialog>
    </AppLayout>
</template>