<?php
namespace Casa\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
/**
 * Class Select2ableTrait
 */
trait Select2ableTrait
{
    //public $displayAttribute = 'nome';
    protected function select2query()
    {
        /** @var Builder $query */
        $query = $this->getQuery();
        return $query->select('id', $this->displayAttribute . ' as text');
    }
    protected function wrapResult($data)
    {
        return ['results' => $data];
    }
    public function select2search(Request $request)
    {
        $searchTerm = $request->get('q');
        $query = $this->select2query();
        if ($searchTerm) {
            $query->where($this->displayAttribute, 'LIKE', "%" . $searchTerm . "%");
        }
        return $this->wrapResult($query->limit(20)->get());
    }
    public function select2searchInit(Request $request)
    {
        $ids = (array) $request->get('ids');
        $query = $this->select2query()->whereIn('id', $ids);
        return $this->wrapResult($query->get());
    }
}