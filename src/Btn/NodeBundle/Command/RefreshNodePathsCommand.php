<?php

namespace Btn\NodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshNodePathsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('btn:node:refresh_paths')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nodeProvider = $this->getContainer()->get('btn_node.provider.node');

        $nodes = $nodeProvider->getRepository()->findAll();

        foreach ($nodes as $node) {
            $node->updateSlugPrefix();
            $node->updateUrl();
            $nodeProvider->save($node, true);
        }
    }
}
