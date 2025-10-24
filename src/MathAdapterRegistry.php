<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

use Cline\Numerus\Adapters\NativeMathAdapter;
use Cline\Numerus\Contracts\MathAdapter;
use WeakMap;

/**
 * Global registry for managing math adapter instances.
 *
 * Provides static access to math adapters with support for both application-wide
 * default adapters and request-scoped adapters. The request-scoped functionality
 * uses WeakMap to automatically garbage-collect adapters when their associated
 * request context is destroyed, preventing memory leaks in long-running processes.
 *
 * This registry pattern allows different parts of an application to use different
 * math adapters based on their precision requirements or performance needs while
 * maintaining a consistent default for general use.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MathAdapterRegistry
{
    /**
     * Request-scoped adapter storage using weak references.
     *
     * Maps request context objects to their associated adapters. Automatically
     * cleans up when context objects are garbage-collected.
     *
     * @var WeakMap<object, MathAdapter>
     */
    private static WeakMap $adapters;

    /**
     * The application-wide default math adapter.
     *
     * Used when no request-specific adapter is configured. Lazy-initialized
     * to NativeMathAdapter on first access if not explicitly set.
     */
    private static ?MathAdapter $defaultAdapter = null;

    /**
     * Set the application-wide default math adapter.
     *
     * Configures the adapter used by all operations unless overridden by a
     * request-scoped adapter. Typically set during application bootstrap.
     *
     * @param MathAdapter $adapter The adapter to use as the default
     */
    public static function setDefault(MathAdapter $adapter): void
    {
        self::$defaultAdapter = $adapter;
    }

    /**
     * Get the application-wide default math adapter.
     *
     * Returns the configured default adapter, or lazily initializes a
     * NativeMathAdapter if no default has been explicitly set.
     *
     * @return MathAdapter The default math adapter instance
     */
    public static function getDefault(): MathAdapter
    {
        return self::$defaultAdapter ??= new NativeMathAdapter();
    }

    /**
     * Reset the default adapter to uninitialized state.
     *
     * Clears the configured default adapter, causing the next call to getDefault()
     * to reinitialize with NativeMathAdapter. Useful for testing scenarios.
     */
    public static function resetDefault(): void
    {
        self::$defaultAdapter = null;
    }

    /**
     * Associate an adapter with a specific request context.
     *
     * Stores an adapter for use within a specific request scope. The adapter is
     * automatically garbage-collected when the request context object is destroyed,
     * making this safe for long-running processes without memory leaks.
     *
     * @param object      $requestContext The request context object to associate with
     * @param MathAdapter $adapter        The adapter to use for this request scope
     */
    public static function setForRequest(object $requestContext, MathAdapter $adapter): void
    {
        self::$adapters ??= new WeakMap();
        self::$adapters[$requestContext] = $adapter;
    }

    /**
     * Get the adapter associated with a request context.
     *
     * Retrieves the adapter configured for a specific request scope, or null
     * if no adapter has been associated with the provided context.
     *
     * @param  object           $requestContext The request context to look up
     * @return null|MathAdapter The associated adapter, or null if none exists
     */
    public static function getForRequest(object $requestContext): ?MathAdapter
    {
        self::$adapters ??= new WeakMap();

        return self::$adapters[$requestContext] ?? null;
    }
}
