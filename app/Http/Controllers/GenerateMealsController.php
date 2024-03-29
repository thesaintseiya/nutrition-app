<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Http\Request;

class GenerateMealsController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'calories' => 'required|integer|min:1000',
            'error' => 'required|integer|gte:0',
            'exclude' => 'nullable|array|max:50',
            'include' => 'nullable|array|max:50',
            'personalMeals' => 'required|boolean',
            'only' => 'nullable|string'
        ]);

        $mealCalories = $validated['calories'] / 3;

        $exclude = $request->exclude;
        $include = $request->include;
        $error = $request->error;
        $personalMeals = $request->personalMeals;
        $only = $request->only;

        if (isset($only)) {
            $values = session('generateMeals');

            $meals = $this->filterMeals($only, $mealCalories, $exclude, $include, $error, $personalMeals);
            $meal = $meals ? collect($meals)->random() : null;

            $totalNewCalories = $values['totalCalories'] - ($values[$only] ? $values[$only]['calories'] : 0) + $this->countCalories($meal, null, null);

            $values['totalCalories'] = round($totalNewCalories);
            $values[$only] = $meal;

            $request->session()->put('generateMeals',  $values);
            return inertia('Home', $values);
        }

        $breakfasts = $this->filterMeals('breakfast', $mealCalories, $exclude, $include, $error, $personalMeals);
        $lunches = $this->filterMeals('lunch', $mealCalories, $exclude, $include, $error, $personalMeals);
        $dinners = $this->filterMeals('dinner', $mealCalories, $exclude, $include, $error, $personalMeals);

        $breakfast = $breakfasts ? collect($breakfasts)->random() : null;
        $lunch = $lunches ? collect($lunches)->random() : null;
        $dinner = $dinners ? collect($dinners)->random() : null;

        $values =  [
            'totalCalories' => $this->countCalories($breakfast, $lunch, $dinner),
            'breakfast' => $breakfast,
            'lunch' => $lunch,
            'dinner' => $dinner,
            'ingredients' => Ingredient::all()
        ];

        $request->session()->put('generateMeals',  $values);
        return inertia('Home', $values);
    }

    public function filterMeals($type, $mealCalories, $excludedIngredients, $includedIngredients, $error, $personalMeals)
    {
        $meals = $this->filterByCalories($type, $mealCalories, $error, $personalMeals);

        if (count($excludedIngredients)) {
            $meals = $this->filterByIngredients($meals, $excludedIngredients);
        }

        if (count($includedIngredients)) {
            $meals = $this->filterByIngredients($meals, $includedIngredients, false);
        }

        return $meals;
    }

    public function filterByCalories($type, $mealCalories, $error, $personalMeals)
    {
        $filterable = $personalMeals && auth()->user() ?
            auth()->user()->meals->where('type', $type) :
            Meal::where('type', $type)->get();

        $meals = $filterable->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'image' => $item->image,
            'ingredients' => $item->ingredients,
            'calories' => round($item->totals['calories']),
        ])->toArray();

        return array_filter($meals, fn ($item) => ($item['calories']
            <= $mealCalories + $error)
            && $item['calories'] >= $mealCalories - $error);
    }

    public function filterByIngredients($meals, $ingredients, $exclude = true)
    {
        $haystackIds = collect($ingredients)->transform(fn ($ing) => $ing['id'])->toArray();

        $meals = array_filter($meals, function ($meal) use ($haystackIds, $exclude) {
            $ingredientsId = $meal['ingredients']->map(fn ($i) => $i['id'])->toArray();
            $result = count(array_intersect($ingredientsId, $haystackIds)) === count($haystackIds);
            return $exclude ? !$result : $result;
        });

        return $meals;
    }

    public function countCalories($breakfast, $lunch, $dinner)
    {
        $breakfast = $breakfast ? $breakfast['calories'] : 0;
        $lunch = $lunch ? $lunch['calories'] : 0;
        $dinner = $dinner ? $dinner['calories'] : 0;

        return round($breakfast + $lunch + $dinner);
    }
}
