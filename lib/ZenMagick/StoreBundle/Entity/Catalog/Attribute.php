<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2012 zenmagick.org
 *
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

namespace ZenMagick\StoreBundle\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;
use ZenMagick\Base\ZMObject;

/**
 * A single attribute.
 *
 * @ORM\Table(name="products_options",
 *  indexes={
 *      @ORM\Index(name="idx_lang_id_zen", columns={"language_id"}),
 *      @ORM\Index(name="idx_products_options_sort_order_zen", columns={"products_options_sort_order"}),
 * *      @ORM\Index(name="idx_products_options_name_zen", columns={"products_options_name"}),
 *  })
 * @ORM\Entity
 * @author DerManoMann
 */
class Attribute extends ZMObject
{
    /**
     * @var integer $attributeId
     *
     * @ORM\Column(name="products_options_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $attributeId;

    /**
     * @var integer $languageId
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageId;

    /**
     * @var string $name
     *
     * @ORM\Column(name="products_options_name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var integer $sortOrder
     *
     * @ORM\Column(name="products_options_sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="products_options_type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var integer $length
     *
     * @ORM\Column(name="products_options_length", type="smallint", nullable=false)
     */
    private $length;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="products_options_comment", type="string", length=64, nullable=true)
     */
    private $comment;

    /**
     * @var integer $size
     *
     * @ORM\Column(name="products_options_size", type="smallint", nullable=false)
     */
    private $size;

    /**
     * @var integer $imagesPerRow
     *
     * @ORM\Column(name="products_options_images_per_row", type="smallint", nullable=true)
     */
    private $imagesPerRow;

    /**
     * @var integer $imageStyle
     *
     * @ORM\Column(name="products_options_images_style", type="smallint", nullable=true)
     */
    private $imageStyle;

    /**
     * @var integer $rows
     *
     * @ORM\Column(name="products_options_rows", type="smallint", nullable=false)
     */
    private $rows;

    private $productId;
    private $values;

    /**
     * Create new attribute.
     *
     * @param int id The id.
     * @param string name The name.
     * @param string type The type.
     */
    public function __construct($id=0, $name=null, $type=null)
    {
        parent::__construct();
        $this->attributeId = $id;
        $this->name = $name;
        $this->type = $type;
        $this->values = array();
    }

    public function getAttributeId() { return $this->attributeId; }

    /**
     * Get the attribute id.
     *
     * @return int The attribute id.
     */
    public function getId() { return $this->attributeId; }

    /**
     * Get the product id.
     *
     * @return int The product id.
     */
    public function getProductId() { return $this->productId; }

    /**
     * Get the attribute name.
     *
     * @return string The attribute name.
     */
    public function getName() { return $this->name; }

    /**
     * Get the attribute type.
     *
     * @return string The attribute type.
     */
    public function getType() { return $this->type; }

    /**
     * Get the attribute sort order.
     *
     * @return int The attribute sort order.
     */
    public function getSortOrder() { return $this->sortOrder; }

    /**
     * Get the attribute comment.
     *
     * @return string The attribute comment.
     */
    public function getComment() { return $this->comment; }

    /**
     * Get the attribute values.
     *
     * @return array A list of <code>ZMAttributeValue</code> objects.
     */
    public function getValues() { return $this->values; }

    /**
     * Set the attribute id.
     *
     * @param int id the attribute id.
     */
    public function setAttributeId($id) { $this->attributeId = $id; }

    /**
     * Set the attribute id.
     *
     * @param int id the attribute id.
     */
    public function setId($id) { $this->attributeId = $id; }

    /**
     * Set the product id.
     *
     * @param int productId The product id.
     */
    public function setProductId($productId) { $this->productId = $productId; }

    /**
     * Set the attribute name.
     *
     * @param string name The attribute name.
     */
    public function setName($name) { $this->name = $name; }

    /**
     * Set the attribute type.
     *
     * @return string The attribute type.
     */
    public function setType($type) { $this->type = $type; }

    /**
     * Set the attribute sort order.
     *
     * @param int sortOrder The attribute sort order.
     */
    public function setSortOrder($sortOrder) { $this->sortOrder = $sortOrder; }

    /**
     * Set the attribute comment.
     *
     * @param string comment The attribute comment.
     */
    public function setComment($comment) { $this->comment = $comment; }

    /**
     * Add an attribute value.
     *
     * @param ZMAttributeValue value A <code>ZMAttributeValue</code>.
     */
    public function addValue($value) { $this->values[] = $value; }

    /**
     * Clear all values.
     */
    public function clearValues()
    {
        $this->values = array();
    }

    /**
     * Remove an attribute value.
     *
     * @param mixed value Either a <code>ZMAttributeValue</code> instance or a value id.
     */
    public function removeValue($value)
    {
        for ($ii=0, $size=count($this->values); $ii < $size; ++$ii) {
            if ((is_object($value) && $value === $this->values[$ii] ) || (is_numeric($value) && (int) $value == $this->values[$ii]->getId())) {
                array_splice($this->values, $ii, 1);
                break;
            }
        }
    }

    /**
     * Check if this attribute is virtual.
     *
     * <p>An attribute can be virtual if, for example, the value is a downloadable file.</p>
     *
     * @return boolean <code>true</code> if, and only if, the attribute is virtual.
     */
    public function isVirtual()
    {
        $attributeService = $this->container->get('attributeService');
        return $attributeService->hasDownloads($this);
    }

}
