<script setup>
import { computed, reactive, ref, watch } from 'vue';
import axios from 'axios';
import GeneralOptions from './GeneralOptions.vue';
import KibanaOptions from './KibanaOptions.vue';
import MachinesSupervisionOptions from './MachinesSupervisionOptions.vue';
import ProcessesSupervisionOptions from './ProcessesSupervisionOptions.vue';
import QueuesSupervisionOptions from './QueuesSupervisionOptions.vue';
import TenantSelectionSidebar from './TenantSelectionSidebar.vue';
import FormStep from '@/Components/FormStep.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    form: Object,
    mode: String,
    automatedProcess: {
        type: Object,
        default: null,
    },
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const orchestratorConnectionConfigurations = computed(() => {
    props.form.orchestrator_connections.forEach(function (orchestratorConnectionConfiguration) {
        const orchestratorConnection = orchestratorConnectionConfiguration.orchestrator_connection;
        orchestratorConnection.tenants.filter(tenant => {
            return orchestratorConnectionConfiguration.tenants.includes(tenant.id);
        }).forEach(function (tenant) {
            if (!tenant.pivot) {
                tenant.pivot = {
                    built_in_alerts: orchestratorConnection.pivot.built_in_alerts,
                    processes_supervision: orchestratorConnection.pivot.processes_supervision,
                    machines_supervision: orchestratorConnection.pivot.machines_supervision,
                    queues_supervision: orchestratorConnection.pivot.queues_supervision,
                    kibana_built_in_alerts: orchestratorConnection.pivot.kibana_built_in_alerts,
                }
            }
        });
    });
    return props.form.orchestrator_connections;
});

const selectedOrchestratorConnectionId = ref(null);
const selectedOrchestratorConnection = computed(() => {
    const configurations = orchestratorConnectionConfigurations.value;
    if (configurations) {
        const configuration = configurations.find(config => config.orchestrator_connection.id === selectedOrchestratorConnectionId.value);
        if (configuration) {
            const orchestratorConnection = configuration.orchestrator_connection;
            Object.assign(selectedOrchestratorConnectionVerificationState, {});
            return orchestratorConnection;
        }
    }
    return {};
});
const selectedOrchestratorConnectionVerificationState = reactive({});

const selectedTenantId = ref(null);
const selectedTenant = computed(() => {
    const configurations = orchestratorConnectionConfigurations.value;
    if (configurations) {
        const configuration = configurations.find(config => config.orchestrator_connection.id === selectedOrchestratorConnectionId.value);
        if (configuration) {
            const orchestratorConnection = configuration.orchestrator_connection;
            if (selectedTenantId.value && orchestratorConnection.tenants) {
                return orchestratorConnection.tenants.find(tenant => tenant.id === selectedTenantId.value);
            }
        }
    }
    return {};
});
const selectedTenantPivot = computed(() => {
    if (selectedTenant.value && selectedTenant.value.pivot) {
        return selectedTenant.value.pivot;
    }
    return {};
});

const selectTenant = (orchestratorConnectionId, id) => {
    selectedOrchestratorConnectionId.value = orchestratorConnectionId;
    selectedTenantId.value = id;
    selectedTenant.value.error = null;
    selectedOrchestratorConnectionVerificationState.message = '';
    selectedOrchestratorConnectionVerificationState.tenantsMessages = [];

    if (!selectedTenant.value.treeviews) {
        getFolders().then(function (res) {
            const orchestratorConnection = res.data.orchestrator_connection;
            selectedOrchestratorConnection.value.verified = orchestratorConnection.verified;
            selectedOrchestratorConnection.value.verified_at_for_humans = orchestratorConnection.verified_at_for_humans;

            const orchestratorConnectionTenant = res.data.orchestrator_connection_tenant;
            selectedTenant.value.verified = orchestratorConnectionTenant.verified;
            selectedTenant.value.verified_at_for_humans = orchestratorConnectionTenant.verified_at_for_humans;

            if (res.data.ok) {
                const folders = res.data.folders;
                if (folders) {
                    selectedTenant.value.treeviews = buildTreeviews(folders);
                    selectedOrchestratorConnectionVerificationState.message = '';
                    selectedOrchestratorConnectionVerificationState.tenantsMessages = [];
                }
            } else {
                const message = res.data.message;
                if (message) {
                    selectedOrchestratorConnectionVerificationState.message = message.verify;
                    selectedOrchestratorConnectionVerificationState.tenantsMessages = message.tenantsResults !== undefined ? message.tenantsResults : [];
                }
                selectedTenant.value.treeviews = null;
            }

            if (props.mode === 'edit' && selectedTenant.value.treeviews) {
                if (!selectedTenant.value.selected_releases) {
                    selectedTenant.value.selected_releases = [];
                }
                if (!selectedTenant.value.selected_machines) {
                    selectedTenant.value.selected_machines = [];
                }
                if (!selectedTenant.value.selected_queues) {
                    selectedTenant.value.selected_queues = [];
                }
                if (selectedTenant.value.treeviews.releases) {
                    updateTreeviewNodesCheckedState(
                        selectedTenant.value.treeviews.releases.nodes,
                        props.automatedProcess.releases.map(release => release.external_id),
                        'release'
                    );
                }
                if (selectedTenant.value.treeviews.machines) {
                    updateTreeviewNodesCheckedState(
                        selectedTenant.value.treeviews.machines.nodes,
                        props.automatedProcess.machines.map(machine => machine.external_id),
                        'machine'
                    );
                }
                if (selectedTenant.value.treeviews.queueDefinitions) {
                    updateTreeviewNodesCheckedState(
                        selectedTenant.value.treeviews.queueDefinitions.nodes,
                        props.automatedProcess.queues.map(queue => queue.external_id),
                        'queue-definition'
                    );
                }
            } else {
                selectedTenant.value.selected_releases = [];
                selectedTenant.value.selected_machines = [];
                selectedTenant.value.selected_queues = [];
            }
            gettingFolders.value = false;
            props.form.processing = false;
        });
    }
};

const gettingFolders = ref(false);
const getFolders = () => {
    gettingFolders.value = true;
    props.form.processing = true;
    return axios.get(route('uipath.folders', {
        orchestrator_connection: selectedOrchestratorConnectionId.value,
        orchestrator_connection_tenant: selectedTenantId.value,
        with_releases: true,
        with_machines: true,
        with_queue_definitions: true,
    }));
};

const buildTreeviews = folders => {
    let roots = [];
    let releasesNodes = {};
    let machinesNodes = {};
    let queueDefinitionsNodes = {};
    let releasesCount = 0;
    let machinesCount = 0;
    let queueDefinitionsCount = 0;

    folders.forEach(function (folder) {
        const folderId = folder.id;
        const folderNodeId = `folder-${folderId}`;
        const folderName = folder.display_name;

        // finding root folders
        if (!folder.parent_id) {
            roots.push(folderNodeId);
        }
        
        // creating folder nodes
        const folderChildren = folders.filter(subfolder => subfolder.parent_id == folderId).map(subfolder => `folder-${subfolder.id}`);
        const folderNode = {
            text: folderName,
            children: folderChildren,
            id: folderNodeId,
        };
        
        releasesNodes[folderNodeId] = JSON.parse(JSON.stringify(folderNode));
        folder.releases && folder.releases.forEach(release => {
            const releaseNodeId = `release-${release.id}`;
            releasesNodes[releaseNodeId] = {
                text: release.name,
            };
            releasesNodes[folderNodeId].children.push(releaseNodeId);
            releasesCount++;
        });
        
        machinesNodes[folderNodeId] = JSON.parse(JSON.stringify(folderNode));
        folder.machines && folder.machines.forEach(machine => {
            const machineNodeId = `machine-${machine.id}`;
            machinesNodes[machineNodeId] = {
                text: machine.name,
            };
            machinesNodes[folderNodeId].children.push(machineNodeId);
            machinesCount++;
        });
        
        queueDefinitionsNodes[folderNodeId] = JSON.parse(JSON.stringify(folderNode));
        folder.queue_definitions && folder.queue_definitions.forEach(queueDefinition => {
            const queueDefinitionNodeId = `queue-definition-${queueDefinition.id}`;
            queueDefinitionsNodes[queueDefinitionNodeId] = {
                text: queueDefinition.name,
            };
            queueDefinitionsNodes[folderNodeId].children.push(queueDefinitionNodeId);
            queueDefinitionsCount++;
        });
    });

    const config = {
        roots: roots,
        keyboardNavigation: false,
        dragAndDrop: false,
        checkboxes: true,
        checkMode: 0,
        editable: false,
        disabled: false,
        padding: 25,
        openedIcon: {
            class: "text-orange-50",
            type: "shape",
            stroke: "currentColor",
            fill: "white",
            strokeWidth: 1.5,
            viewBox: "0 0 24 24",
            draw: "M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776",
        },
        closedIcon: {
            class: "text-orange-50",
            type: "shape",
            stroke: "currentColor",
            fill: "white",
            strokeWidth: 1.5,
            viewBox: "0 0 24 24",
            draw: `M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z`,
        },
    };

    // cleaning empty folders and removing empty folders from children
    // entry = [0] => id, [1] => text, children, state, ...
    const releasesFolderNodeIdsToRemove = Object.entries(releasesNodes).filter(entry => entry[0].startsWith('folder-') && entry[1].children.length == 0).map(entry => entry[0]);
    Object.keys(releasesNodes).forEach(key => {
        let node = releasesNodes[key];
        if (node.children) {
            node.children = node.children.filter(child => !releasesFolderNodeIdsToRemove.includes(child));
        }
        releasesNodes[key] = node;
    });
    const machinesFolderNodeIdsToRemove = Object.entries(machinesNodes).filter(entry => entry[0].startsWith('folder-') && entry[1].children.length == 0).map(entry => entry[0]);
    Object.keys(machinesNodes).forEach(key => {
        let node = machinesNodes[key];
        if (node.children) {
            node.children = node.children.filter(child => !machinesFolderNodeIdsToRemove.includes(child));
        }
        machinesNodes[key] = node;
    });
    const queueDefinitionsFolderNodeIdsToRemove = Object.entries(queueDefinitionsNodes).filter(entry => entry[0].startsWith('folder-') && entry[1].children.length == 0).map(entry => entry[0]);
    Object.keys(queueDefinitionsNodes).forEach(key => {
        let node = queueDefinitionsNodes[key];
        if (node.children) {
            node.children = node.children.filter(child => !queueDefinitionsFolderNodeIdsToRemove.includes(child));
        }
        queueDefinitionsNodes[key] = node;
    });

    // cleaning empty root folders
    const releasesConfig = JSON.parse(JSON.stringify(config));
    releasesConfig.roots = releasesConfig.roots.filter(id => !releasesFolderNodeIdsToRemove.includes(id));
    const machinesConfig = JSON.parse(JSON.stringify(config));
    machinesConfig.roots = machinesConfig.roots.filter(id => !machinesFolderNodeIdsToRemove.includes(id));
    const queueDefinitionsConfig = JSON.parse(JSON.stringify(config));
    queueDefinitionsConfig.roots = queueDefinitionsConfig.roots.filter(id => !queueDefinitionsFolderNodeIdsToRemove.includes(id));

    return {
        releases: {
            nodes: releasesCount > 0 ? releasesNodes : {},
            config: releasesConfig,
        },
        machines: {
            nodes: machinesCount > 0 ? machinesNodes : {},
            config: machinesConfig,
        },
        queueDefinitions: {
            nodes: queueDefinitionsCount > 0 ? queueDefinitionsNodes : {},
            config: queueDefinitionsConfig,
        },
    };
};

const resetTreeviewCheckedState = treeview => {
    if (treeview) {
        for (const id in treeview.nodes) {
            treeview.nodes[id].state.checked = false;
        }
    }
};

const updateTreeviewNodesCheckedState = (nodes, selected, type) => {
    //console.log(`type: ${type}`);

    if (selected) {
        // process leaves (release, machine or queue)
        for (const id in nodes) {
            if (!id.startsWith('folder-') && selected.includes(id.replace(`${type}-`, ''))) {
                nodes[id].state = {
                    checked: true,
                };
            }
        }

        // process folders
        selected.forEach(id => {
            // get node id
            const nodeId = `${type}-${id}`;

            // get folders with a selected entity in its children
            const folders = Object.entries(nodes).filter(entry => entry[0].startsWith('folder-') && entry[1].children.includes(nodeId));

            folders.forEach(folder => {
                let parent = undefined;
                let folderNodeId = folder[0];

                do {
                    // get the node corresponding to the folder
                    const folderNode = nodes[folderNodeId];
                    //console.log(`folder node corresponding to the folder ${folderNodeId}: ${JSON.stringify(folderNode)}`);

                    // get the nodes corresponding to folder children
                    const folderChildrenNodes = [];
                    folderNode.children.forEach(child => folderChildrenNodes.push(nodes[child]));
                    //console.log(`nodes corresponding to folder children: ${JSON.stringify(folderChildrenNodes)}`);

                    // set folder checked state to true if all children have checked state to true
                    const checkedChildNodes = folderChildrenNodes.filter(childNode => childNode.state && childNode.state.checked);
                    const indeterminateChildNodes = folderChildrenNodes.filter(childNode => childNode.state && childNode.state.indeterminate);

                    const checked = checkedChildNodes.length === folderChildrenNodes.length;
                    //console.log(`checked: ${checked}`);

                    // set folder indeterminate state to true if at least one of the children (but not all) have checked state or indeterminate state to true
                    const indeterminate = !checked && (
                        (checkedChildNodes.length > 0 && checkedChildNodes.length < folderChildrenNodes.length)
                        || (indeterminateChildNodes.length > 0)
                    );
                    //console.log(`indeterminate: ${indeterminate}`);

                    folderNode.state = {
                        checked: checked,
                        indeterminate: indeterminate,
                    };

                    // if folder checked state was passed to true, then get its parent folder corresponding node
                    if (checked) {
                        parent = Object.entries(nodes).find(entry => entry[1].children && entry[1].children.includes(folderNodeId));
                        //console.log(`parent: ${JSON.stringify(parent)}`);
                        if (parent) {
                            folderNodeId = parent[0];
                        }
                    } else {
                        break;
                    }
                } while (parent !== undefined)
            });
        });
    }
};

const addReleaseToSelectedTenant = (node) => {
    addEntityToSelectedTenant(node.id, node, 'release', selectedTenant.value.selected_releases, selectedTenant.value.treeviews.releases);
};
const removeReleaseFromSelectedTenant = (node) => {
    removeEntityFromSelectedTenant(node.id, node, 'release', selectedTenant.value.selected_releases, selectedTenant.value.treeviews.releases);
};
const addMachineToSelectedTenant = (node) => {
    addEntityToSelectedTenant(node.id, node, 'machine', selectedTenant.value.selected_machines, selectedTenant.value.treeviews.machines);
};
const removeMachineFromSelectedTenant = (node) => {
    removeEntityFromSelectedTenant(node.id, node, 'machine', selectedTenant.value.selected_machines, selectedTenant.value.treeviews.machines);
};
const addQueueToSelectedTenant = (node) => {
    addEntityToSelectedTenant(node.id, node, 'queue-definition', selectedTenant.value.selected_queues, selectedTenant.value.treeviews.queueDefinitions);
};
const removeQueueFromSelectedTenant = (node) => {
    removeEntityFromSelectedTenant(node.id, node, 'queue-definition', selectedTenant.value.selected_queues, selectedTenant.value.treeviews.queueDefinitions);
};

const addEntityToSelectedTenant = (id, node, type, selected, treeview) => {
    const nodeType = id.startsWith('folder') ? 'folder' : type;

    if (nodeType === 'folder') {
        node.children.filter(child => child.startsWith(type)).map(child => child.replace(`${type}-`, '')).forEach(id => {
            if (!selected.map(item => item.id).includes(id)) {
                selected.push({
                    id: id,
                    folder: node.id.replace('folder-', ''),
                });
            }
        });
        node.children.filter(child => child.startsWith('folder')).forEach(child => {
            // search for node in treeview
            const n = Object.entries(treeview.nodes).find(([key, value]) => key == child)[1];
            // call add entity (recursively)
            addEntityToSelectedTenant(child, n, type, selected, treeview);
        });
    } else {
        const cleanedId = id.replace(`${type}-`, '');
        if (!selected.map(item => item.id).includes(cleanedId)) {
            selected.push({
                id: cleanedId,
                folder: node.parent.replace('folder-', ''),
            });
        }
    }
};

const removeEntityFromSelectedTenant = (id, node, type, selected, treeview) => {
    const nodeType = id.startsWith('folder') ? 'folder' : type;

    if (nodeType === 'folder') {
        node.children.forEach(child => {
            if (child.startsWith('folder')) {
                // search for node in treeview
                const n = Object.entries(treeview.nodes).find(([key, value]) => key == child)[1];
                // call remove entity (recursively)
                removeEntityFromSelectedTenant(child, n, type, selected, treeview);
            } else {
                selected.splice(selected.map(item => item.id).indexOf(child.replace(`${type}-`, '')), 1);
            }
        });
    } else {
        selected.splice(selected.map(item => item.id).indexOf(node.id.replace(`${type}-`, '')), 1);
    }
};

watch(selectedTenantPivot, pivot => {
    if (pivot) {
        if (!pivot.processes_supervision && selectedTenant.value.selected_releases && selectedTenant.value.selected_releases.length > 0) {
            resetTreeviewCheckedState(selectedTenant.value.treeviews.releases);
            selectedTenant.value.selected_releases.splice(0, selectedTenant.value.selected_releases.length);
        }
        if (!pivot.machines_supervision && selectedTenant.value.selected_machines && selectedTenant.value.selected_machines.length > 0) {
            resetTreeviewCheckedState(selectedTenant.value.treeviews.machines);
            selectedTenant.value.selected_machines.splice(0, selectedTenant.value.selected_machines.length);
        }
        if (!pivot.queues_supervision && selectedTenant.value.selected_queues && selectedTenant.value.selected_queues.length > 0) {
            resetTreeviewCheckedState(selectedTenant.value.treeviews.queues);
            selectedTenant.value.selected_queues.splice(0, selectedTenant.value.selected_queues.length);
        }
    }
}, {
    deep: true,
});
</script>

<template>
    <SectionBorder v-if="withTopBorder" />
    <FormStep>
        <template #title>
            {{ __('UiPath Orchestrator related entities configuration') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Non beatae, aspernatur incidunt, hic vel ea
                    modi reiciendis cumque eos itaque numquam suscipit exercitationem earum voluptates illo. Vero
                    laborum illo id.
                </p>
            </div>

            <TenantSelectionSidebar :select-tenant="selectTenant" :orchestrator-connection-configurations="orchestratorConnectionConfigurations"
                :selected-orchestrator-connection-verification-state="selectedOrchestratorConnectionVerificationState"
                :selected-orchestrator-connection-id="selectedOrchestratorConnectionId" :selected-tenant="selectedTenant"
                :getting-folders="gettingFolders" />
        </template>

        <template #form v-if="selectedTenant && selectedTenant.id">
            <div v-if="selectedOrchestratorConnectionVerificationState.tenantsMessages && selectedOrchestratorConnectionVerificationState.tenantsMessages.find(value => value.id === selectedTenant.id)"
                class="col-span-6">
                <Toast :id="`tenant-${selectedTenant.id}`" level="error" text-size="xs"
                    :message="selectedOrchestratorConnectionVerificationState.tenantsMessages.find(value => value.id === selectedTenant.id).error" :closable="false" />
            </div>

            <div v-if="gettingFolders" class="col-span-6 text-center">
                <svg aria-hidden="true" role="status"
                    class="inline mr-3 w-4 h-4 text-blue-50 animate-spin" viewBox="0 0 100 101"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="#E5E7EB" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentColor" />
                </svg>
            </div>

            <div v-if="selectedTenant.verified && !gettingFolders" class="col-span-6 grid grid-cols-6 gap-4">
                <!-- <GeneralOptions :selected-tenant="selectedTenant" /> -->
                
                <ProcessesSupervisionOptions :selected-tenant="selectedTenant" :add-release-to-selected-tenant="addReleaseToSelectedTenant" :remove-release-from-selected-tenant="removeReleaseFromSelectedTenant" />
                
                <MachinesSupervisionOptions :selected-tenant="selectedTenant" :add-machine-to-selected-tenant="addMachineToSelectedTenant" :remove-machine-from-selected-tenant="removeMachineFromSelectedTenant" />
                
                <QueuesSupervisionOptions :selected-tenant="selectedTenant" :add-queue-to-selected-tenant="addQueueToSelectedTenant" :remove-queue-from-selected-tenant="removeQueueFromSelectedTenant" />
                
                <KibanaOptions :selected-orchestrator-connection="selectedOrchestratorConnection" :selected-tenant="selectedTenant" />
            </div>
        </template>
    </FormStep>
</template>