<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import UiTittle from '@/components/ui/UiTittle.vue';
import UiCard from '@/components/ui/UiCardDashboard.vue';
import { DollarSign, Info, Landmark, Users, Weight } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const page = usePage();
const { totalMembers, totalPatrimonialMembers, totalRevenue, totalMembersWithPendentPayment } = page.props;

const chartDataFirstSemester = ref();
const chartDataSecondSemester = ref();
const chartOptions = ref();

const setChartData = (months, revenueData) => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: months,
        datasets: [
            {
                type: 'bar',
                label: 'Pago',
                backgroundColor: documentStyle.getPropertyValue('--color-green-400'),
                borderColor: documentStyle.getPropertyValue('--color-green-400'),
                data: revenueData.totalPaid
            },
            {
                type: 'bar',
                label: 'Vencido',
                backgroundColor: documentStyle.getPropertyValue('--color-red-400'),
                borderColor: documentStyle.getPropertyValue('--color-red-400'),
                data: revenueData.totalExpired
            }
        ]
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--color-gray-500');
    const textColorSecondary = documentStyle.getPropertyValue('--color-gray-500');
    const surfaceBorder = documentStyle.getPropertyValue('--color-gray-300');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.8,
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        const value = context.parsed.y || 0;
                        const formatted = new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(value);
                        return `${context.dataset.label}: ${formatted}`;
                    }
                }
            },
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                stacked: true,
                ticks: {
                    color: textColorSecondary,
                    font: {
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                stacked: true,
                ticks: {
                    color: textColorSecondary,
                    callback: function(value) {
                        return new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL',
                            maximumFractionDigits: 0
                        }).format(value);
                    }
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    };
}

onMounted(() => {
    const { monthlyRevenues } = page.props;
    const revenuesDataFirstSemester = monthlyRevenues.slice(0, 6);
    const revenuesDataSecondSemester = monthlyRevenues.slice(6);
    const monthNameFirst = [];
    const monthNameSecond = [];
    const revenueDataFirst = {
        totalPaid: [],
        totalExpired: [],
    }
    const revenueDataSecond = {
        totalPaid: [],
        totalExpired: [],
    }

    revenuesDataFirstSemester.forEach((item) => {
        monthNameFirst.push(item.month_name);
        revenueDataFirst.totalPaid.push(item.total_paid);
        revenueDataFirst.totalExpired.push(item.total_expired);
    });

    revenuesDataSecondSemester.forEach((item) => {
        monthNameSecond.push(item.month_name);
        revenueDataSecond.totalPaid.push(item.total_paid);
        revenueDataSecond.totalExpired.push(item.total_expired);
    });

    chartDataFirstSemester.value = setChartData(monthNameFirst, revenueDataFirst);
    chartDataSecondSemester.value = setChartData(monthNameSecond, revenueDataSecond);
    chartOptions.value = setChartOptions();
});

</script>

<template>
    <AppLayout>
        <InertiaHead title="Dashboard" />

        <UiTittle :text="'\u{1F4CA} Dashboard'" />
        <div class="grid gap-8 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1">
            <UiCard
                title="Total de Membros"
                :content="totalMembers.toString()"
                footer="Membros cadastrados no sistema"
                :icon="Users"
                class-icon="w-6! h-6! text-blue-500"
            />
            <UiCard
                title="Pagamentos Pendentes"
                :content="totalMembersWithPendentPayment.toString()"
                footer="Mensalidades Vencidas"
                :icon="Info"
                class-icon="w-6! h-6! text-red-500"
            />
            <UiCard
                title="Títulos Patrimoniais"
                :content="totalPatrimonialMembers.toString()"
                footer="Membros patrimoniais"
                :icon="Landmark"
                class-icon="w-6! h-6! text-orange-500"
            />
            <UiCard
                title="Receita Total"
                :content="'R$ ' + totalRevenue"
                footer="Receita total do clube"
                :icon="DollarSign"
                class-icon="w-6! h-6! text-green-500"
            />
        </div>
        <Panel>
            <template #header>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Visão Geral das Mensalidades</h2>
                    <h3 class="text-gray-500">Consulta anual da receita de mensalidades do clube</h3>
                </div>
            </template>
            <div class="flex flex-col items-center">
                <h2 class="text-xl font-bold mt-4">1º Semestre</h2>
                <div class="card mb-4">
                    <Chart type="bar" :data="chartDataFirstSemester" :options="chartOptions" class="h-[24rem] w-[48rem]"  />
                </div>
                <h2 class="text-xl font-bold mt-4">2º Semestre</h2>
                <div class="card">
                    <Chart type="bar" :data="chartDataSecondSemester" :options="chartOptions" class="h-[24rem] w-[48rem]"  />
                </div>
            </div>
        </Panel>
    </AppLayout>
</template>
