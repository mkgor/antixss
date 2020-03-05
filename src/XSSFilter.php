<?php

namespace Mgor;

use Illuminate\Http\Request;

/**
 * Class XSSFilter
 */
class XSSFilter
{
    /**
     * @var array
     */
    protected $except = [];

    /**
     * Отключает экранирование для параметра(ов), возможно задать условие с помощью callback-а, в который передается
     * класс Request
     *
     * @param  array  $exceptArray
     * @return $this
     */
    public function except(array $exceptArray)
    {
        foreach ($exceptArray as $key => $value) {
            if (is_numeric($key)) {
                $callback = function () {
                    return false;
                };
            } else {
                $callback = $value;
            }

            $this->except[$key] = $callback;
        }


        return $this;
    }

    /**
     * Экранирует параметры запроса и возвращает запрос
     *
     * @param  Request  $request
     * @return Request
     */
    public function filter($request)
    {
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, array_keys($this->except))
                || (in_array($key, array_keys($this->except)) && $this->except[$key]($request))) {
                $value = htmlspecialchars($value);

            }

            $request->merge([$key => $value]);
        }

        return $request;
    }
}
