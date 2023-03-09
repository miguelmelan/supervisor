<script setup>
import { computed, reactive } from 'vue';
import Actions from './Actions.vue';
import Dropdown from './Dropdown.vue';
import SelectedTable from './SelectedTable.vue';
import SelectedTenantsDefaultOptions from './SelectedTenantsDefaultOptions.vue';
import SelectedTenantsTable from './SelectedTenantsTable.vue';
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import SectionBorder from '@/Components/SectionBorder.vue';

const props = defineProps({
    mode: String,
    form: Object,
    orchestratorConnections: Array,
    orchestratorConnectionsSelection: Array,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const selection = reactive({
    orchestrator_connection: null,
    tenants: [],
});

const selectOrchestratorConnection = (orchestratorConnection) => {
    const selected = alreadySelectedOrchestratorConnection(orchestratorConnection.id);
    if (selected) {
        Object.assign(selection, selected);
    } else {
        selection.orchestrator_connection = orchestratorConnection;
        selection.tenants = orchestratorConnection.tenants.map(function (item) {
            return item.id;
        });
    }
};

const resetOrchestratorConnectionSelection = () => {
    Object.assign(selection, {
        orchestrator_connection: null,
        tenants: [],
    });
};

const selectAllOrchestratorConnectionTenants = computed({
    get() {
        return selection.tenants.length > 0
            ? selection.tenants.length == selection.orchestrator_connection.tenants.length
            : false;
    },
    set(value) {
        let values = [];
        if (value) {
            selection.orchestrator_connection.tenants.forEach(function (tenant) {
                values.push(tenant.id);
            });
        }
        selection.tenants = values;
    }
});

const addSelectedOrchestratorConnectionToSelection = () => {
    const selected = alreadySelectedOrchestratorConnection(selection.orchestrator_connection.id);
    if (selected) {
        Object.assign(selected, selection);
    } else {
        props.form.orchestrator_connections.push({ ...selection });
    }
    resetOrchestratorConnectionSelection();
};

const editOrchestratorConnectionFromSelection = (id) => {
    const selected = alreadySelectedOrchestratorConnection(id);
    Object.assign(selection, selected);
}

const removeOrchestratorConnectionFromSelection = (id) => {
    props.form.orchestrator_connections = props.form.orchestrator_connections.filter(function (item) {
        return item.orchestrator_connection.id !== id;
    });
};

const alreadySelectedOrchestratorConnection = (id) => {
    const selected = props.form.orchestrator_connections.find(function (item) {
        return item.orchestrator_connection.id === id;
    });

    return selected;
};
</script>
    
<template>
    <SectionBorder v-if="withTopBorder" />
    <FormStep>
        <template #title>
            {{ __('UiPath Orchestrator connections selection') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Non beatae, aspernatur incidunt, hic vel ea
                    modi reiciendis cumque eos itaque numquam suscipit exercitationem earum voluptates illo. Vero
                    laborum illo id.
                </p>
            </div>
        </template>

        <template #form>
            <!-- selected orchestrator connections -->
            <SelectedTable :form="form" :edit="editOrchestratorConnectionFromSelection"
                :remove="removeOrchestratorConnectionFromSelection" />

            <!-- add orchestrator connection -->
            <div class="col-span-6 sm:col-span-4">
                <!-- dropdown list -->
                <Dropdown :selection="selection" :orchestratorConnections="props.orchestratorConnections"
                    :select="selectOrchestratorConnection" :reset="resetOrchestratorConnectionSelection"
                    :alreadySelected="alreadySelectedOrchestratorConnection" />
                <InputError :message="form.errors.orchestrator_connections" class="mt-2" />

                <!-- tenants table -->
                <SelectedTenantsTable :selection="selection" :selectAll="selectAllOrchestratorConnectionTenants" />
            </div>

            <!-- selected tenants default options -->
            <SelectedTenantsDefaultOptions :selection="selection" />

            <!-- actions -->
            <Actions :form="form" :selection="selection" :add="addSelectedOrchestratorConnectionToSelection"
                :remove="removeOrchestratorConnectionFromSelection" :reset="resetOrchestratorConnectionSelection"
                :alreadySelected="alreadySelectedOrchestratorConnection" />
        </template>
    </FormStep>
</template>