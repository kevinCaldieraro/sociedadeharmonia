<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import UiTittle from '@/components/ui/UiTittle.vue';
import { usePage } from '@inertiajs/vue3';

const { user } = usePage().props.auth;

const getGreetings = () => {
    const currDate = new Date();
    const hour = currDate.getHours();

    if (hour >= 4 && hour < 12) {
        return 'Bom dia';
    } else if (hour >= 12 && hour < 18) {
        return 'Boa tarde';
    } else {
        return 'Boa noite';
    }
}
</script>

<template>
    <AppLayout>
        <InertiaHead title="Página Inicial" />
        <UiTittle :text="'\u{1F3E0} Página Inicial'" />
        <Panel>
            <template #header>
                <div>
                    <h2 class="text-2xl font-bold mb-2">{{ '\u{1F44B} ' + getGreetings() }}, {{ user.name }}!</h2>
                    <h3 class="text-gray-500">Bem-vindo ao sistema de gerenciamento da Associação Harmonia Ivoti</h3>
                </div>
            </template>
            <p class="mt-5 mb-4">Use o menu lateral para navegar entre as diferentes seções:</p>
            <ul class="flex flex-col gap-2 list-disc ml-4">
                <li><b>Dashboard:</b> Tenha uma visão geral dos registros do clube</li>
                <li><b>Membros:</b> Gerencie os membros do clube, visualize status e cadastre novos membros</li>
                <li><b>Mensalidades:</b> Controle de pagamentos e histórico financeiro</li>
                <li><b>Usuários do Sistema:</b> Gerencie os usuários do sistema (apenas administradores)</li>
            </ul>
        </Panel>
    </AppLayout>

</template>
