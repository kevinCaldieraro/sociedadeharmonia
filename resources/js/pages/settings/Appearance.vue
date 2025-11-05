<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/UserSettingsLayout.vue';
import SelectColorModeButton from '@/components/SelectColorModeButton.vue';

const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: 'Aparência' },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <InertiaHead title="Aparência" />

        <SettingsLayout>
            <Card
                pt:body:class="max-w-2xl space-y-3"
                pt:caption:class="space-y-1"
            >
                <template #title>
                    Configurações de aparência
                </template>
                <template #subtitle>
                    Atualize sua preferência de tema
                </template>
                <template #content>
                    <SelectColorModeButton id="color-mode-selector" />
                </template>
            </Card>
        </SettingsLayout>
    </AppLayout>
</template>
