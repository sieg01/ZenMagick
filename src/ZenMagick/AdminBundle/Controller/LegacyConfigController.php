<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2012 zenmagick.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace ZenMagick\AdminBundle\Controller;

use ZenMagick\ZenMagickBundle\Controller\DefaultController;

use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller for legacy config.
 *
 * @author DerManoMann <mano@zenmagick.org>
 */
class LegacyConfigController extends DefaultController
{
    /**
     * {@inheritDoc}
     */
    public function showGroupValuesAction($groupId)
    {
        $configService = $this->get('configWidgetService');
        $group = $configService->getConfigGroupForId($groupId);
        $groupValues = $configService->getValuesForGroupId($groupId);

        return $this->findView(null, array('group' => $group, 'groupValues' => $groupValues));
    }

    /**
     * {@inheritDoc}
     */
    public function updateGroupValuesAction(Request $request)
    {
        $groupId = $request->request->get('groupId');

        $configService = $this->container->get('configWidgetService');
        $group = $configService->getConfigGroupForId($groupId);
        $groupValues = $configService->getValuesForGroupId($groupId);

        // update changed
        $updated = array();
        foreach ($groupValues as $widget) {
            $name = $widget->getName();
            $oldValue = $widget->getValue();
            if (null !== ($newValue = $request->request->get($name)) && $newValue != $oldValue) {
                // update
                $configService->updateConfigValue($name, $newValue);
                $updated[] = $widget->getTitle();
            }
        }
        if (0 < count($updated)) {
            $message = $this->get('translator')->trans('Sucessfully updated: %config_values%.', array('%config_values%' => implode(', ', $updated)));
            $this->get('session.flash_bag')->success($message);
        }

        // 'parameter' is a property on the view class...
        $tpl = array('group' => $group, 'groupValues' => $groupValues);

        return $this->findView('success', $tpl, array('parameter' => 'groupId='.$groupId));
    }

}
