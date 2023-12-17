<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Livestock;

/**
 * LivestockSearch represents the model behind the search form of `backend\models\Livestock`.
 */
class LivestockSearch extends Livestock
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id', 'bcs'], 'integer'],
            [['eid', 'vid', 'name', 'birthdate', 'gender', 'age', 'chest_size', 'body_weight', 'health', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Livestock::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'birthdate' => $this->birthdate,
            'type_of_livestock_id' => $this->type_of_livestock_id,
            'breed_of_livestock_id' => $this->breed_of_livestock_id,
            'maintenance_id' => $this->maintenance_id,
            'source_id' => $this->source_id,
            'ownership_status_id' => $this->ownership_status_id,
            'reproduction_id' => $this->reproduction_id,
            'bcs' => $this->bcs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'eid', $this->eid])
            ->andFilterWhere(['like', 'vid', $this->vid])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'age', $this->age])
            ->andFilterWhere(['like', 'chest_size', $this->chest_size])
            ->andFilterWhere(['like', 'body_weight', $this->body_weight])
            ->andFilterWhere(['like', 'health', $this->health]);

        return $dataProvider;
    }
}
