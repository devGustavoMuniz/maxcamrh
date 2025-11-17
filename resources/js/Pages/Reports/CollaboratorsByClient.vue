<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { FileText, FileSpreadsheet } from "lucide-vue-next";

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const form = useForm({
    client_id: "",
    format: "pdf",
});

const generateReport = (format) => {
    if (!form.client_id) {
        return;
    }

    // Usar Axios para fazer o download
    window.axios.post(route('reports.collaborators-by-client.generate'), {
        client_id: form.client_id,
        format: format
    }, {
        responseType: 'blob'
    }).then(response => {
        // Criar URL do blob
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Extrair nome do arquivo do header Content-Disposition
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'relatorio.' + (format === 'csv' ? 'csv' : 'html');

        if (contentDisposition) {
            // Remove aspas e espaços extras do nome do arquivo
            const filenameMatch = contentDisposition.match(/filename=([^;\s]+)/i);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '');
            }
        }

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    }).catch(error => {
        console.error('Erro ao gerar relatório:', error);
        if (error.response && error.response.data) {
            alert('Erro ao gerar relatório. Tente novamente.');
        }
    });
};
</script>

<template>
    <Head title="Relatório: Colaboradores por Cliente" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Relatório: Colaboradores por Cliente
            </h2>
        </template>

        <div class="mx-auto w-full space-y-6">
            <!-- Filtros do Relatório -->
            <Card>
                <CardHeader>
                    <CardTitle>Gerar Relatório</CardTitle>
                    <CardDescription>
                        Selecione um cliente para visualizar os colaboradores
                        vinculados
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="client">Cliente *</Label>
                                <Select v-model="form.client_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione um cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="client in clients"
                                                :key="client.id"
                                                :value="client.id"
                                            >
                                                {{ client.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.client_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.client_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex flex-wrap gap-2">
                            <Button
                                @click="generateReport('csv')"
                                :disabled="!form.client_id || form.processing"
                                variant="default"
                            >
                                <FileSpreadsheet class="mr-2 h-4 w-4" />
                                Exportar CSV
                            </Button>
                            <Button
                                @click="generateReport('pdf')"
                                :disabled="!form.client_id || form.processing"
                                variant="outline"
                            >
                                <FileText class="mr-2 h-4 w-4" />
                                Exportar PDF
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
