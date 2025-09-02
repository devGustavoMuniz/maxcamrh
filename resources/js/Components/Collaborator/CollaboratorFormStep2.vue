<script setup>
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    collaboratorForm: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    collaborator: {
        personal_email: props.collaboratorForm.collaborator.personal_email,
        business_email: props.collaboratorForm.collaborator.business_email,
        phone: props.collaboratorForm.collaborator.phone,
        cellphone: props.collaboratorForm.collaborator.cellphone,
        emergency_phone: props.collaboratorForm.collaborator.emergency_phone,
    },
    address: {
        cep: props.collaboratorForm.address.cep,
        street: props.collaboratorForm.address.street,
        number: props.collaboratorForm.address.number,
        complement: props.collaboratorForm.address.complement,
        neighborhood: props.collaboratorForm.address.neighborhood,
        city: props.collaboratorForm.address.city,
        state: props.collaboratorForm.address.state,
    },
});

async function fetchAddressByCep() {
    if (form.address.cep && form.address.cep.replace(/\D/g, "").length === 8) {
        try {
            const response = await fetch(
                `https://viacep.com.br/ws/${form.address.cep.replace(/\D/g, "")}/json/`,
            );
            if (!response.ok) throw new Error("CEP não encontrado");
            const data = await response.json();
            if (data.erro) throw new Error("CEP inválido");
            form.address.street = data.logradouro;
            form.address.neighborhood = data.bairro;
            form.address.city = data.localidade;
            form.address.state = data.uf;
            document.getElementById("address_number")?.focus();
        } catch (error) {
            console.error("Erro ao buscar CEP:", error);
        }
    }
}
</script>

<template>
    <section class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <Label for="collaborator_personal_email">Email Pessoal</Label>
                <Input
                    id="collaborator_personal_email"
                    v-model="form.collaborator.personal_email"
                    type="email"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.personal_email']"
                />
            </div>
            <div>
                <Label for="collaborator_business_email">Email Comercial</Label>
                <Input
                    id="collaborator_business_email"
                    v-model="form.collaborator.business_email"
                    type="email"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.business_email']"
                />
            </div>
            <div>
                <Label for="collaborator_phone">Telefone Fixo</Label>
                <Input
                    id="collaborator_phone"
                    v-model="form.collaborator.phone"
                    v-mask="['(##) ####-####', '(##) #####-####']"
                    type="tel"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.phone']"
                />
            </div>
            <div>
                <Label for="collaborator_cellphone">Celular</Label>
                <Input
                    id="collaborator_cellphone"
                    v-model="form.collaborator.cellphone"
                    v-mask="['(##) ####-####', '(##) #####-####']"
                    type="tel"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.cellphone']"
                />
            </div>
            <div class="md:col-span-2">
                <Label for="collaborator_emergency_phone"
                    >Telefone de Emergência</Label
                >
                <Input
                    id="collaborator_emergency_phone"
                    v-model="form.collaborator.emergency_phone"
                    v-mask="['(##) ####-####', '(##) #####-####']"
                    type="tel"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['collaborator.emergency_phone']"
                />
            </div>
            <div>
                <Label for="address_cep">CEP</Label>
                <Input
                    id="address_cep"
                    v-model="form.address.cep"
                    v-mask="'#####-###'"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    @blur="fetchAddressByCep"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.cep']"
                />
            </div>
            <div>
                <Label for="address_street">Logradouro (Rua, Av.)</Label>
                <Input
                    id="address_street"
                    v-model="form.address.street"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.street']"
                />
            </div>
            <div>
                <Label for="address_number">Número</Label>
                <Input
                    id="address_number"
                    v-model="form.address.number"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.number']"
                />
            </div>
            <div>
                <Label for="address_complement">Complemento</Label>
                <Input
                    id="address_complement"
                    v-model="form.address.complement"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.complement']"
                />
            </div>
            <div>
                <Label for="address_neighborhood">Bairro</Label>
                <Input
                    id="address_neighborhood"
                    v-model="form.address.neighborhood"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.neighborhood']"
                />
            </div>
            <div>
                <Label for="address_city">Cidade</Label>
                <Input
                    id="address_city"
                    v-model="form.address.city"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.city']"
                />
            </div>
            <div>
                <Label for="address_state">Estado (UF)</Label>
                <Input
                    id="address_state"
                    v-model="form.address.state"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-700"
                    maxlength="2"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['address.state']"
                />
            </div>
        </div>
    </section>
</template>
