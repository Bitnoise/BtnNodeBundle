<?php

namespace Btn\NodeBundle\Provider;

interface NodeContentProviderInterface
{
    public function getName();

    public function getForm();

    public function isEnabled();

    public function resolveRoute($dataForm = array());

    public function resolveRouteParameters($dataForm = array());

    public function resolveControlRoute($dataForm = array());

    public function resolveControlRouteParameters($dataForm = array());
}
