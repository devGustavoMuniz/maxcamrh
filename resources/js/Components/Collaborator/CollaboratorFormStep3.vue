<script setup>
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Textarea } from "@/Components/ui/textarea";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    collaboratorForm: {
        type: Object,
        required: true,
    },
});

const form = props.collaboratorForm;

const moneyConfig = {
    decimal: ",",
    thousands: ".",
    prefix: "R$ ",
    precision: 2,
    masked: false,
};
</script>

<template>
    <section class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="collaborator_department">Departamento</Label>
                <Input
                    id="collaborator_department"
                    v-model="form.collaborator.department"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.department']"
                />
            </div>
            <div>
                <Label for="collaborator_position">Cargo</Label>
                <Input
                    id="collaborator_position"
                    v-model="form.collaborator.position"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.position']"
                />
            </div>
            <div>
                <Label for="collaborator_type_of_contract"
                    >Tipo de Contrato</Label
                >
                <Select v-model="form.collaborator.type_of_contract">
                    <SelectTrigger class="bg-white dark:bg-gray-700">
                        <SelectValue placeholder="Selecione..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="CLT">CLT</SelectItem>
                        <SelectItem value="PJ">PJ</SelectItem>
                        <SelectItem value="Estágio">Estágio</SelectItem>
                        <SelectItem value="Temporário">Temporário</SelectItem>
                    </SelectContent>
                </Select>
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.type_of_contract']"
                />
            </div>
            <div>
                <Label for="collaborator_salary">Salário (R$)</Label>
                <Input
                    id="collaborator_salary"
                    v-model="form.collaborator.salary"
                    v-money3="moneyConfig"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.salary']"
                />
            </div>
            <div>
                <Label for="collaborator_admission_date"
                    >Data de Admissão</Label
                >
                <Input
                    id="collaborator_admission_date"
                    v-model="form.collaborator.admission_date"
                    type="date"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.admission_date']"
                />
            </div>
            <div>
                <Label for="collaborator_contract_start_date"
                    >Data Início Efetivo Contrato</Label
                >
                <Input
                    id="collaborator_contract_start_date"
                    v-model="form.collaborator.contract_start_date"
                    type="date"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.contract_start_date']"
                />
            </div>
            <div>
                <Label for="collaborator_contract_expiration"
                    >Data Fim do Contrato (opcional)</Label
                >
                <Input
                    id="collaborator_contract_expiration"
                    v-model="form.collaborator.contract_expiration"
                    type="date"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.contract_expiration']"
                />
            </div>
            <div>
                <Label for="collaborator_direct_superior_name"
                    >Nome do Superior Direto</Label
                >
                <Input
                    id="collaborator_direct_superior_name"
                    v-model="form.collaborator.direct_superior_name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.direct_superior_name']"
                />
            </div>
            <div class="md:col-span-2">
                <Label for="collaborator_hierarchical_degree"
                    >Grau Hierárquico</Label
                >
                <Input
                    id="collaborator_hierarchical_degree"
                    v-model="form.collaborator.hierarchical_degree"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.hierarchical_degree']"
                />
            </div>
            <div class="md:col-span-2">
                <Label for="collaborator_observations"
                    >Observações Contratuais</Label
                >
                <Textarea
                    id="collaborator_observations"
                    v-model="form.collaborator.observations"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    rows="3"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.observations']"
                />
            </div>
        </div>
    </section>
</template>
