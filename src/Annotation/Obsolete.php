<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * For safely drop column from table which used in several services.
 * The property should not be displayed in schema but should remain in the tableThe property should not be in schema.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class Obsolete {}
