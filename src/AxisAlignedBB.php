<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\math;

use const PHP_INT_MAX;

class AxisAlignedBB{

	/** @var float */
	public $minX;
	/** @var float */
	public $minY;
	/** @var float */
	public $minZ;
	/** @var float */
	public $maxX;
	/** @var float */
	public $maxY;
	/** @var float */
	public $maxZ;

	public function __construct(float $minX, float $minY, float $minZ, float $maxX, float $maxY, float $maxZ){
		if($minX > $maxX){
			throw new \InvalidArgumentException("minX $minX is larger than maxX $maxX");
		}
		if($minY > $maxY){
			throw new \InvalidArgumentException("minY $minY is larger than maxY $maxY");
		}
		if($minZ > $maxZ){
			throw new \InvalidArgumentException("minZ $minZ is larger than maxZ $maxZ");
		}
		$this->minX = $minX;
		$this->minY = $minY;
		$this->minZ = $minZ;
		$this->maxX = $maxX;
		$this->maxY = $maxY;
		$this->maxZ = $maxZ;
	}

	/**
	 * Returns a new AxisAlignedBB extended by the specified X, Y and Z.
	 * If each of X, Y and Z are positive, the relevant max bound will be increased. If negative, the relevant min
	 * bound will be decreased.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return AxisAlignedBB
	 */
	public function addCoord(float $x, float $y, float $z) : AxisAlignedBB{
		$minX = $this->minX;
		$minY = $this->minY;
		$minZ = $this->minZ;
		$maxX = $this->maxX;
		$maxY = $this->maxY;
		$maxZ = $this->maxZ;

		if($x < 0){
			$minX += $x;
		}elseif($x > 0){
			$maxX += $x;
		}

		if($y < 0){
			$minY += $y;
		}elseif($y > 0){
			$maxY += $y;
		}

		if($z < 0){
			$minZ += $z;
		}elseif($z > 0){
			$maxZ += $z;
		}

		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}

	/**
	 * Outsets the bounds of this AxisAlignedBB by the specified X, Y and Z.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return $this
	 */
	public function expand(float $x, float $y, float $z){
		$this->minX -= $x;
		$this->minY -= $y;
		$this->minZ -= $z;
		$this->maxX += $x;
		$this->maxY += $y;
		$this->maxZ += $z;

		return $this;
	}

	/**
	 * Returns an expanded clone of this AxisAlignedBB.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return AxisAlignedBB
	 */
	public function expandedCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->expand($x, $y, $z);
	}

	/**
	 * Shifts this AxisAlignedBB by the given X, Y and Z.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return $this
	 */
	public function offset(float $x, float $y, float $z){
		$this->minX += $x;
		$this->minY += $y;
		$this->minZ += $z;
		$this->maxX += $x;
		$this->maxY += $y;
		$this->maxZ += $z;

		return $this;
	}

	/**
	 * Returns an offset clone of this AxisAlignedBB.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return AxisAlignedBB
	 */
	public function offsetCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->offset($x, $y, $z);
	}

	/**
	 * Insets the bounds of this AxisAlignedBB by the specified X, Y and Z.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return $this
	 */
	public function contract(float $x, float $y, float $z){
		$this->minX += $x;
		$this->minY += $y;
		$this->minZ += $z;
		$this->maxX -= $x;
		$this->maxY -= $y;
		$this->maxZ -= $z;

		return $this;
	}

	/**
	 * Returns a contracted clone of this AxisAlignedBB.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 *
	 * @return AxisAlignedBB
	 */
	public function contractedCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->contract($x, $y, $z);
	}

	/**
	 * Extends the AABB in the given direction.
	 *
	 * @param int   $face
	 * @param float $distance Negative values pull the face in, positive values push out.
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function extend(int $face, float $distance) : AxisAlignedBB{
		if($face === Facing::DOWN){
			$this->minY -= $distance;
		}elseif($face === Facing::UP){
			$this->maxY += $distance;
		}elseif($face === Facing::NORTH){
			$this->minZ -= $distance;
		}elseif($face === Facing::SOUTH){
			$this->maxZ += $distance;
		}elseif($face === Facing::WEST){
			$this->minX -= $distance;
		}elseif($face === Facing::EAST){
			$this->maxX += $distance;
		}else{
			throw new \InvalidArgumentException("Invalid face $face");
		}

		return $this;
	}

	/**
	 * Returns an extended clone of this bounding box.
	 * @see AxisAlignedBB::extend()
	 *
	 * @param int   $face
	 * @param float $distance
	 *
	 * @return AxisAlignedBB
	 * @throws \InvalidArgumentException
	 */
	public function extendedCopy(int $face, float $distance) : AxisAlignedBB{
		return (clone $this)->extend($face, $distance);
	}

	/**
	 * Inverse of extend().
	 * @see AxisAlignedBB::extend()
	 *
	 * @param int   $face
	 * @param float $distance Positive values pull the face in, negative values push out.
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function trim(int $face, float $distance) : AxisAlignedBB{
		return $this->extend($face, -$distance);
	}

	/**
	 * Returns a trimmed clone of this bounding box.
	 * @see AxisAlignedBB::trim()
	 *
	 * @param int   $face
	 * @param float $distance
	 *
	 * @return AxisAlignedBB
	 * @throws \InvalidArgumentException
	 */
	public function trimmedCopy(int $face, float $distance) : AxisAlignedBB{
		return $this->extendedCopy($face, -$distance);
	}

	/**
	 * Increases the dimension of the AABB along the given axis.
	 *
	 * @param int   $axis one of the Facing::AXIS_* constants
	 * @param float $distance Negative values reduce width, positive values increase width.
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function stretch(int $axis, float $distance) : AxisAlignedBB{
		if($axis === Facing::AXIS_Y){
			$this->minY -= $distance;
			$this->maxY += $distance;
		}elseif($axis === Facing::AXIS_Z){
			$this->minZ -= $distance;
			$this->maxZ += $distance;
		}elseif($axis === Facing::AXIS_X){
			$this->minX -= $distance;
			$this->maxX += $distance;
		}else{
			throw new \InvalidArgumentException("Invalid axis $axis");
		}
		return $this;
	}

	/**
	 * Returns a stretched copy of this bounding box.
	 * @see AxisAlignedBB::stretch()
	 *
	 * @param int   $axis
	 * @param float $distance
	 *
	 * @return AxisAlignedBB
	 * @throws \InvalidArgumentException
	 */
	public function stretchedCopy(int $axis, float $distance) : AxisAlignedBB{
		return (clone $this)->stretch($axis, $distance);
	}

	/**
	 * Reduces the dimension of the AABB on the given axis. Inverse of stretch().
	 * @see AxisAlignedBB::stretch()
	 *
	 * @param int   $axis
	 * @param float $distance
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function squash(int $axis, float $distance) : AxisAlignedBB{
		return $this->stretch($axis, -$distance);
	}

	/**
	 * Returns a squashed copy of this bounding box.
	 * @see AxisAlignedBB::squash()
	 *
	 * @param int   $axis
	 * @param float $distance
	 *
	 * @return AxisAlignedBB
	 * @throws \InvalidArgumentException
	 */
	public function squashedCopy(int $axis, float $distance) : AxisAlignedBB{
		return $this->stretchedCopy($axis, -$distance);
	}

	public function calculateXOffset(AxisAlignedBB $bb, float $x) : float{
		if($bb->maxY <= $this->minY or $bb->minY >= $this->maxY){
			return $x;
		}
		if($bb->maxZ <= $this->minZ or $bb->minZ >= $this->maxZ){
			return $x;
		}
		if($x > 0 and $bb->maxX <= $this->minX){
			$x1 = $this->minX - $bb->maxX;
			if($x1 < $x){
				$x = $x1;
			}
		}elseif($x < 0 and $bb->minX >= $this->maxX){
			$x2 = $this->maxX - $bb->minX;
			if($x2 > $x){
				$x = $x2;
			}
		}

		return $x;
	}

	public function calculateYOffset(AxisAlignedBB $bb, float $y) : float{
		if($bb->maxX <= $this->minX or $bb->minX >= $this->maxX){
			return $y;
		}
		if($bb->maxZ <= $this->minZ or $bb->minZ >= $this->maxZ){
			return $y;
		}
		if($y > 0 and $bb->maxY <= $this->minY){
			$y1 = $this->minY - $bb->maxY;
			if($y1 < $y){
				$y = $y1;
			}
		}elseif($y < 0 and $bb->minY >= $this->maxY){
			$y2 = $this->maxY - $bb->minY;
			if($y2 > $y){
				$y = $y2;
			}
		}

		return $y;
	}

	public function calculateZOffset(AxisAlignedBB $bb, float $z) : float{
		if($bb->maxX <= $this->minX or $bb->minX >= $this->maxX){
			return $z;
		}
		if($bb->maxY <= $this->minY or $bb->minY >= $this->maxY){
			return $z;
		}
		if($z > 0 and $bb->maxZ <= $this->minZ){
			$z1 = $this->minZ - $bb->maxZ;
			if($z1 < $z){
				$z = $z1;
			}
		}elseif($z < 0 and $bb->minZ >= $this->maxZ){
			$z2 = $this->maxZ - $bb->minZ;
			if($z2 > $z){
				$z = $z2;
			}
		}

		return $z;
	}

	/**
	 * Returns whether any part of the specified AABB is inside (intersects with) this one.
	 *
	 * @param AxisAlignedBB $bb
	 * @param float         $epsilon
	 *
	 * @return bool
	 */
	public function intersectsWith(AxisAlignedBB $bb, float $epsilon = 0.00001) : bool{
		if($bb->maxX - $this->minX > $epsilon and $this->maxX - $bb->minX > $epsilon){
			if($bb->maxY - $this->minY > $epsilon and $this->maxY - $bb->minY > $epsilon){
				return $bb->maxZ - $this->minZ > $epsilon and $this->maxZ - $bb->minZ > $epsilon;
			}
		}

		return false;
	}

	/**
	 * Returns whether the specified vector is within the bounds of this AABB on all axes.
	 *
	 * @param Vector3 $vector
	 * @return bool
	 */
	public function isVectorInside(Vector3 $vector) : bool{
		if($vector->x <= $this->minX or $vector->x >= $this->maxX){
			return false;
		}
		if($vector->y <= $this->minY or $vector->y >= $this->maxY){
			return false;
		}

		return $vector->z > $this->minZ and $vector->z < $this->maxZ;
	}

	/**
	 * Returns the mean average of the AABB's X, Y and Z lengths.
	 * @return float
	 */
	public function getAverageEdgeLength() : float{
		return ($this->maxX - $this->minX + $this->maxY - $this->minY + $this->maxZ - $this->minZ) / 3;
	}

	/**
	 * Returns the interior volume of the AABB.
	 *
	 * @return float
	 */
	public function getVolume() : float{
		return ($this->maxX - $this->minX) * ($this->maxY - $this->minY) * ($this->maxZ - $this->minZ);
	}

	/**
	 * Returns whether the specified vector is within the Y and Z bounds of this AABB.
	 *
	 * @param Vector3 $vector
	 * @return bool
	 */
	public function isVectorInYZ(Vector3 $vector) : bool{
		return $vector->y >= $this->minY and $vector->y <= $this->maxY and $vector->z >= $this->minZ and $vector->z <= $this->maxZ;
	}

	/**
	 * Returns whether the specified vector is within the X and Z bounds of this AABB.
	 *
	 * @param Vector3 $vector
	 * @return bool
	 */
	public function isVectorInXZ(Vector3 $vector) : bool{
		return $vector->x >= $this->minX and $vector->x <= $this->maxX and $vector->z >= $this->minZ and $vector->z <= $this->maxZ;
	}

	/**
	 * Returns whether the specified vector is within the X and Y bounds of this AABB.
	 *
	 * @param Vector3 $vector
	 * @return bool
	 */
	public function isVectorInXY(Vector3 $vector) : bool{
		return $vector->x >= $this->minX and $vector->x <= $this->maxX and $vector->y >= $this->minY and $vector->y <= $this->maxY;
	}

	/**
	 * Performs a ray-trace and calculates the point on the AABB's edge nearest the start position that the ray-trace
	 * collided with. Returns a RayTraceResult with colliding vector closest to the start position.
	 * Returns null if no colliding point was found.
	 *
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 *
	 * @return RayTraceResult|null
	 */
	public function calculateIntercept(Vector3 $pos1, Vector3 $pos2) : ?RayTraceResult{
		$v1 = $pos1->getIntermediateWithXValue($pos2, $this->minX);
		$v2 = $pos1->getIntermediateWithXValue($pos2, $this->maxX);
		$v3 = $pos1->getIntermediateWithYValue($pos2, $this->minY);
		$v4 = $pos1->getIntermediateWithYValue($pos2, $this->maxY);
		$v5 = $pos1->getIntermediateWithZValue($pos2, $this->minZ);
		$v6 = $pos1->getIntermediateWithZValue($pos2, $this->maxZ);

		if($v1 !== null and !$this->isVectorInYZ($v1)){
			$v1 = null;
		}

		if($v2 !== null and !$this->isVectorInYZ($v2)){
			$v2 = null;
		}

		if($v3 !== null and !$this->isVectorInXZ($v3)){
			$v3 = null;
		}

		if($v4 !== null and !$this->isVectorInXZ($v4)){
			$v4 = null;
		}

		if($v5 !== null and !$this->isVectorInXY($v5)){
			$v5 = null;
		}

		if($v6 !== null and !$this->isVectorInXY($v6)){
			$v6 = null;
		}

		$vector = null;
		$distance = PHP_INT_MAX;

		foreach([$v1, $v2, $v3, $v4, $v5, $v6] as $v){
			if($v !== null and ($d = $pos1->distanceSquared($v)) < $distance){
				$vector = $v;
				$distance = $d;
			}
		}

		if($vector === null){
			return null;
		}

		$f = -1;

		if($vector === $v1){
			$f = Facing::WEST;
		}elseif($vector === $v2){
			$f = Facing::EAST;
		}elseif($vector === $v3){
			$f = Facing::DOWN;
		}elseif($vector === $v4){
			$f = Facing::UP;
		}elseif($vector === $v5){
			$f = Facing::NORTH;
		}elseif($vector === $v6){
			$f = Facing::SOUTH;
		}

		return new RayTraceResult($this, $f, $vector);
	}

	public function __toString(){
		return "AxisAlignedBB({$this->minX}, {$this->minY}, {$this->minZ}, {$this->maxX}, {$this->maxY}, {$this->maxZ})";
	}

	/**
	 * Returns a 1x1x1 bounding box starting at grid position 0,0,0.
	 *
	 * @return AxisAlignedBB
	 */
	public static function one() : AxisAlignedBB{
		return new AxisAlignedBB(0, 0, 0, 1, 1, 1);
	}
}
