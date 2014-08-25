<?php

namespace Btn\NodeBundle;

final class NodeEvents
{
    /**
     * Event is triggerd when new node was created
     */
    const NODE_CREATED      = 'btn_node.node_created';
    /**
     * Event is triggerd when node was updated
     */
    const NODE_UPDATED      = 'btn_node.node_updated';
    /**
     * Event is triggerd when node was deleted
     */
    const NODE_DELETED      = 'btn_node.node_deleted';
    /**
     * Event is triggerd when node provider was changed
     */
    const PROVIDER_CHANGED  = 'btn_node.provider_changed';
    /**
     * Event is triggerd when node provider parameters were modified
     */
    const PROVIDER_MODIFIED = 'btn_node.provider_modified';
}
